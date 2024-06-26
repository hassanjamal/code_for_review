<?php

namespace App\PlatformAPI\Mindbody;

use App\AccessToken;
use App\Appointment;
use App\Client;
use App\Exceptions\PlatformGatewayException;
use App\Location;
use App\PlatformAPI\BaseGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\ProviderClass;
use App\Staff;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Zttp\Zttp;

class MindbodyGateway extends BaseGateway implements PlatformGateway
{
    protected $paginationParams;

    /**
     * @param $siteId
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function getActivationCode($siteId)
    {
        try {
            $response = $this->getApiClient($siteId)->get('site/activationcode')->json();

            return [
                'code' => $response['ActivationCode'],
                'link' => $response['ActivationLink'],
            ];
        } catch (Exception $e) {
            // We can add more codes and messages here as necessary to set the message of the PlatformGatewayException.
            throw new PlatformGatewayException('Something went wrong while getting the activation code.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param $siteId
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function authenticateApp($siteId)
    {
        try {
            $accessStoredInDatabase = AccessToken::forSite($siteId)->first();

            if ($accessStoredInDatabase && $accessStoredInDatabase->token === "access-token") {
                // TODO need to implement the token validity, access tokens should be per user instead of site wide.
                return $accessStoredInDatabase;
            }

            $response = $this->getApiClient($siteId)->post('usertoken/issue', [
                'Username' => config('platform.mindbody.app_username'),
                'Password' => config('platform.mindbody.app_password'),
            ])->json();

            return AccessToken::updateOrCreate(['site_id' => $siteId], ['token' => data_get($response, 'AccessToken')]);
        } catch (Exception $e) {
            if ($e->getCode() === 403) { // This is MINDBODY's code for authentication failure.
                throw new PlatformGatewayException('App Authentication failed.', Response::HTTP_UNAUTHORIZED);
            }

            throw new PlatformGatewayException('Something went wrong while getting App authentication token.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Take user credentials and return an staff model for user with their access token.
     *
     * @param $username
     * @param $password
     * @param $siteId
     * @return Staff
     * @throws \App\Exceptions\PlatformGatewayException
     */
    public function authenticateStaff($siteId, $username, $password)
    {
        try {
            $response = $this->getApiClient($siteId)->post('usertoken/issue', [
                'Username' => $username,
                'Password' => $password,
            ])->json();

            $staff = new Staff();
            $staff->api_id = data_get($response, 'User.Id');
            $staff->first_name = data_get($response, 'User.FirstName');
            $staff->last_name = data_get($response, 'User.LastName');
            $staff->api_role = strtolower(data_get($response, 'User.Type'));
            $staff->api_access_token = data_get($response, 'AccessToken');

            return $staff;
        } catch (Exception $e) {
            if ($e->getCode() === 403) { // This is MINDBODY's code for authentication failure.
                throw new PlatformGatewayException('Authentication failed.', Response::HTTP_UNAUTHORIZED);
            }

            throw new PlatformGatewayException('Something went wrong while getting authentication token.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param $siteId
     * @param $staffId
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function getStaffMember($siteId, $staffId)
    {
        return $this->getStaffMembers($siteId, [$staffId])->first();
    }

    /**
     * @param       $siteId
     * @param array $staffIds
     * @return \Illuminate\Support\Collection|mixed|\Tightenco\Collect\Support\Collection
     * @throws PlatformGatewayException
     */
    public function getStaffMembers($siteId, array $staffIds = [])
    {
        $this->validateSiteId($siteId);

        $token = $this->authenticateStaff($siteId, config('platform.mindbody.app_username'), config('platform.mindbody.app_password'))->api_access_token;

        try {
            if (! empty($staffIds)) {
                $queryParams = ['request.staffIds' => $staffIds];
            }

            $response = $this->getApiClient($siteId)->withHeaders(['authorization' => $token])->get('staff/staff', $queryParams ?? [])->json();

            return collect(data_get($response, 'StaffMembers', []))->filter(function ($staff) {
                return $this->isValidStaff($staff);
            })->map(function ($apiStaff) {
                $staff = new Staff();
                $staff->api_id = data_get($apiStaff, 'Id');
                $staff->first_name = data_get($apiStaff, 'FirstName');
                $staff->last_name = data_get($apiStaff, 'LastName');
                $staff->api_access_token = null;

                return $staff;
            });
        } catch (Exception $e) {
            // Can add more logic here to change the message for different types of exceptions that come back from the API.
            // Examples: Staff not found, Site Id not valid, Network down. We can test for these as they arise throughout development.
            // To start we will just throw an exception with generic message and status code.
            throw new PlatformGatewayException('Something went wrong while retrieving staff members.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param $siteId
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function getLocations($siteId)
    {
        $this->validateSiteId($siteId);

        try {
            $response = $this->getApiClient($siteId)->get('site/locations')->json();

            return collect(data_get($response, 'Locations', []))->map(function ($apiLocation) {
                $location = new Location();
                $location->api_id = data_get($apiLocation, 'Id');
                $location->name = data_get($apiLocation, 'Name');
                $location->address = data_get($apiLocation, 'Address');
                $location->address_2 = data_get($apiLocation, 'Address2');
                $location->phone = data_get($apiLocation, 'Phone');
                $location->city = data_get($apiLocation, 'City');
                $location->state_province = data_get($apiLocation, 'StateProvCode');
                $location->postal_code = data_get($apiLocation, 'PostalCode');
                $location->latitude = data_get($apiLocation, 'Latitude');
                $location->longitude = data_get($apiLocation, 'Longitude');

                return $location;
            });
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting locations.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getClients($siteId, $lastModifiedDate = null, $clientIds = [])
    {
        $this->validateSiteId($siteId);

        try {
            $queryParams = [];
            if (! empty($clientIds)) {
                $queryParams = array_merge($queryParams, ['ClientIds' => $clientIds]);
            }
            if ($lastModifiedDate) {
                $queryParams = array_merge($queryParams, ['LastModifiedDate' => $lastModifiedDate->toIso8601String()]);
            }

            $response = $this->getApiClientWithAuthHeader($siteId)->get($this->getApiUrl('client/clients'), $queryParams)->json();

            $property = Property::findByApiIdentifier($siteId)->first();

            $collection = collect(data_get($response, 'Clients', []))->map(function ($apiClient) use ($property) {
                $client = new Client();
                $client->property_id = $property->id;
                $client->api_id = data_get($apiClient, 'UniqueId');
                $client->api_public_id = data_get($apiClient, 'Id');
                $client->first_name = data_get($apiClient, 'FirstName');
                $client->last_name = data_get($apiClient, 'LastName');
                $client->gender = data_get($apiClient, 'Gender');
                $client->email = data_get($apiClient, 'Email');
                $client->birth_date = $this->makeDate(data_get($apiClient, 'BirthDate'));
                $client->referred_by = data_get($apiClient, 'ReferredBy');
                $client->first_appointment_date = $this->makeDate(data_get($apiClient, 'FirstAppointmentDate'));
                $client->photo_url = data_get($apiClient, 'PhotoUrl');
                $client->status = data_get($apiClient, 'Status');
                $client->membership_id = data_get($apiClient, 'MembershipIcon');
                $client->membership_name = 'add-this';

                // Generate the composite key for the client.
                $client->id = $client->makeCompositeKey();

                return $client;
            });

            $collection->pagination = data_get($response, 'PaginationResponse');

            return $collection;
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting clients.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Gets a list of referral types. Referral types are options that new clients can choose to
     * identify how they learned about the business. Referral types are typically used for the sign-up process.
     *
     * @param $siteId
     * @throws PlatformGatewayException
     */
    public function getClientReferralTypes($siteId)
    {
        $this->validateSiteId($siteId);

        try {
            $queryParams = ['IncludeInactive' => true];
            $response = $this->getApiClientWithAuthHeader($siteId)->get('client/clientreferraltypes', $queryParams)->json();
            dd($response);

            //            return collect(data_get($response, 'ReferralTypes', []))->map(function ($apiClient) {
            //                $clientReferralTypes = new ClientReferralTypes();
            //                return $clientReferralTypes;
            //            });
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting client referral types.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * TODO -- This api call to mindbody need to be revisited @hassan @dustin
     *
     * @param $siteId
     * @param $data
     * @throws PlatformGatewayException
     */
    public function getActiveClientMemberships($siteId, $data)
    {
        $this->validateSiteId($siteId);

        try {
            $queryParams = [
                'ClientId' => data_get($data, 'ClientId'),
                'LocationId ' => data_get($data, 'LocationId'),
            ];
            $response = $this->getApiClientWithAuthHeader($siteId)->get('client/activeclientmemberships', $queryParams)->json();
            // TODO - need to ask dustin about client active membership .
            dd($response);

            //            return collect(data_get($response, 'ClientMemberships', []))->map(function ($apiClient) {
            //                            $clientMemberships = new ClientMemberships();
            //                            return $clientMemberships;
            //            });
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting client active membership', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getSessionTypes($siteId)
    {
        $this->validateSiteId($siteId);

        try {
            $response = $this->getApiClientWithAuthHeader($siteId)->get('site/sessiontypes')->json();

            return collect(data_get($response, 'SessionTypes', []))->pluck('Name', 'Id')->all();
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting Session Types.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getMemberships($siteId)
    {
        try {
            $this->validateSiteId($siteId);

            $key = sprintf('%s:memberships', $siteId);

            return cache()->remember($key, now()->addHours(5), function () use ($siteId) {
                $memberships = collect($this->getApiClientWithAuthHeader($siteId)->get('site/memberships')->json());

                return $memberships->flatten(1)->map(function ($m) {
                    return ['id' => $m['MembershipId'], 'name' => $m['MembershipName']];
                });
            });
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting memberships.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getAppointments($siteId, Carbon $startDate, Carbon $endDate, array $staffIds = [], string $clientId = null, array $locationIds = [], array $appointmentIds = [])
    {
        $this->validateSiteId($siteId);

        try {
            $queryParams['StartDate'] = optional($startDate)->toDateTimeLocalString();
            $queryParams['EndDate'] = optional($endDate)->toDateTimeLocalString();
            $queryParams['StaffIds'] = $staffIds;
            $queryParams['ClientId'] = $clientId;
            $queryParams['LocationIds'] = $locationIds;
            $queryParams['AppointmentIds'] = $appointmentIds;

            $response = $this->getApiClientWithAuthHeader($siteId)->get($this->getApiUrl('appointment/staffappointments'), $queryParams)->json();

            $collection = $this->mapApiValues($siteId, collect(data_get($response, 'Appointments', [])));

            $collection->pagination = data_get($response, 'PaginationResponse');

            return $collection;
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting staff appointment.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateAppointment($siteId, $appointment)
    {
        $this->validateSiteId($siteId);

        try {
            $response = $this->getApiClientWithAuthHeader($siteId)->post('appointment/updateappointment', [
                'AppointmentId' => $appointment->api_id,
                'Notes' => $appointment->notes,
            ])->json();

            return $this->mapApiValues($siteId, collect([data_get($response, 'Appointment', [])]))->first();
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting updating the appointment.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getClasses(
        $siteId,
        Carbon $startDate,
        Carbon $endDate,
        $staffIds = [],
        $locationIds = [],
        $classIds = []
    ) {
        if (! in_array($siteId, [-99787, 16134])) {
            throw new PlatformGatewayException('The site id is not valid.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $queryParams = [
                'StartDateTime' => optional($startDate)->toDateTimeLocalString(),
                'EndDateTime' => optional($endDate)->toDateTimeLocalString(),
            ];
            if (! empty($staffIds)) {
                array_merge($queryParams, ['StaffIds' => $staffIds]);
            }
            if (! empty($locationIds)) {
                array_merge($queryParams, ['LocationIds' => $locationIds]);
            }
            if (! empty($classIds)) {
                array_merge($queryParams, ['ClassIds' => $classIds]);
            }

            $response = $this->getApiClientWithAuthHeader($siteId)->get('class/classes', $queryParams)->json();

            return collect(data_get($response, 'Classes', []))->map(function ($apiClass) use ($siteId) {
                $providerClass = new ProviderClass();

                $providerClass->is_canceled = data_get($apiClass, 'IsCanceled');
                $providerClass->api_id = data_get($apiClass, 'Id');
                $providerClass->api_instance_id = data_get($apiClass, 'ClassScheduleId');
                $providerClass->start_date_time = Carbon::parse(data_get($apiClass, 'StartDateTime'));
                $providerClass->end_date_time = Carbon::parse(data_get($apiClass, 'EndDateTime'));
                $providerClass->resource_name = data_get($apiClass, 'Resource.Name');
                $providerClass->class_description_name = data_get($apiClass, 'ClassDescription.Name');
                $providerClass->staff_api_id = data_get($apiClass, 'Staff.Id');
                $providerClass->location_api_id = data_get($apiClass, 'Location.Id');

                try {
                    $providerClass->clients = $this->getClassVisits($siteId, data_get($apiClass, 'Id'));
                } catch (Exception $e) {
                    $providerClass->clients = collect();
                    Log::critical("Getting visits for Class Id ".data_get($apiClass, 'Id').' has failed');
                }

                return $providerClass;
            });
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting classes.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function validateSiteId($siteId)
    {
        // Filter through tenant's sites and make sure they have the site we are looking for.
        $tenantHasSite = collect(tenant()->data)->filter(function ($item, $key) {
            return Str::contains($key, 'mb:');
        })->contains($siteId);

        throw_if(! $tenantHasSite, PlatformGatewayException::class, 'The site id is not valid.', 422);

        return $tenantHasSite;
    }

    public function withPagination($take, $skip)
    {
        $this->paginationParams = sprintf('?limit=%s&offset=%s', $take, $skip);

        return $this;
    }

    protected function getApiUrl($route)
    {
        return $route.$this->paginationParams;
    }

    protected function getClassVisits($siteId, $classId)
    {
        $this->validateSiteId($siteId);

        try {
            $queryParams = [
                'ClassID' => $classId,
            ];

            $response = $this->getApiClientWithAuthHeader($siteId)->get('class/classvisits', $queryParams)->json();

            return collect(data_get($response, 'Class.Visits', []))->map(function ($apiClassVisit) {
                $providerClassVisit = [];
                $providerClassVisit['client_api_public_id'] = data_get($apiClassVisit, 'ClientId');
                $providerClassVisit['signed_in'] = data_get($apiClassVisit, 'SignedIn');

                return $providerClassVisit;
            });
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting class schedule.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param $siteId
     * @return Zttp
     */
    protected function getApiClient($siteId)
    {
        if (! $this->apiClient) {
            $this->apiClient = Zttp::withOptions(config('platform.mindbody.api_client_options'))->withHeaders([
                'SiteId' => $siteId,
            ]);
        }

        return $this->apiClient;
    }

    protected function getApiClientWithAuthHeader($siteId)
    {
        if (! $this->apiClient) {
            $this->apiClient = Zttp::withOptions(config('platform.mindbody.api_client_options'))->withHeaders([
                'SiteId' => $siteId,
                'Authorization' => $this->authenticateApp($siteId)->token,
            ]);
        }

        return $this->apiClient;
    }

    protected function isValidStaff($staff)
    {
        return data_get($staff, 'Id') > 1;
    }

    protected function makeDate($value)
    {
        if (is_string($value)) {
            return Carbon::parse($value);
        }
    }

    protected function getRoomName($apiAppointment)
    {
        $resources = collect(data_get($apiAppointment, 'Resources', []))->firstWhere('Name');

        return data_get($resources, 'Name');
    }

    protected function mapApiValues($siteId, $appointmentsCollection)
    {
        $property = Property::with('locations:id,api_id,property_id')->findByApiIdentifier($siteId)->first();
        $sessionTypes = $this->getSessionTypes($siteId);

        return $appointmentsCollection->map(function ($apiAppointment) use (
            $property,
            $sessionTypes
        ) {
            return new Appointment([
                'id' => makeTripleCompositeKey($property->id, data_get($apiAppointment, 'LocationId'), data_get($apiAppointment, 'Id')),
                'api_id' => (string) data_get($apiAppointment, 'Id'),
                'property_id' => $property->id,
                'location_api_id' => data_get($apiAppointment, 'LocationId'),
                'location_id' => $property->locations->where('api_id', data_get($apiAppointment, 'LocationId'))
                                                     ->first()->id,
                'client_api_public_id' => data_get($apiAppointment, 'ClientId'),
                'staff_api_id' => data_get($apiAppointment, 'StaffId'),
                'staff_id' => makeDoubleCompositeKey($property->id, data_get($apiAppointment, 'StaffId')),
                'duration' => data_get($apiAppointment, 'Duration'),
                'status' => data_get($apiAppointment, 'Status'),
                'start_date_time' => Carbon::parse(data_get($apiAppointment, 'StartDateTime')),
                'end_date_time' => Carbon::parse(data_get($apiAppointment, 'EndDateTime')),
                'notes' => data_get($apiAppointment, 'Notes'),
                'staff_requested' => data_get($apiAppointment, 'StaffRequested', false),
                'service_id' => data_get($apiAppointment['SessionTypeId'], null),
                'service_name' => data_get($sessionTypes, $apiAppointment['SessionTypeId'], 'Unknown Visit Type'),
                'room_name' => $this->getRoomName($apiAppointment),
                'first_appointment' => data_get($apiAppointment, 'FirstAppointment'),
            ]);
        });
    }
}
