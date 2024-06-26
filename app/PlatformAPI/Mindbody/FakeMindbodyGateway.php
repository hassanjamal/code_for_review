<?php

namespace App\PlatformAPI\Mindbody;

use App\AccessToken;
use App\Appointment;
use App\Client;
use App\Exceptions\PlatformGatewayException;
use App\Location;
use App\PlatformAPI\Mindbody\Traits\SetFakeMindbodyData;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\ProviderClass;
use App\Staff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class FakeMindbodyGateway implements PlatformGateway
{
    use SetFakeMindbodyData;

    public $locations;

    public $staff;

    public $clients;

    public $sessionTypes;

    public $appointments;

    public $take;

    public $skip;

    public $providerClasses;

    public function __construct()
    {
        $this->setLocations();
        $this->setStaff();
        $this->setClient();
        $this->setPropertySessionTypes();
        $this->setAppointments();
        $this->setProviderClasses();
    }

    public function getActivationCode($siteId)
    {
        try {
            return [
                16134 => ['code' => 'code-for:16134', 'link' => 'link-for:16134'],
                -99787 => ['code' => 'code-for:-99787', 'link' => 'link-for:-99787'],
            ][$siteId];
        } catch (\Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting the activation code.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function authenticateApp($siteId)
    {
        if (! $this->validateSiteId($siteId)) {
            throw new PlatformGatewayException('Something went wrong while getting authentication token.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response = $this->validateLogin(config('platform.mindbody.app_username'), config('platform.mindbody.app_password'));

        if (! $response) {
            throw new PlatformGatewayException('Authentications failed.', Response::HTTP_UNAUTHORIZED);
        }

        $accessStoredInDatabase = AccessToken::forSite($siteId)->first();
        if ($accessStoredInDatabase && $accessStoredInDatabase->token === "access-token") {
            // TODO need to implement the token validity
            return $accessStoredInDatabase;
        }

        return AccessToken::updateOrCreate(['site_id' => $siteId], ['token' => 'access-token']);
    }

    public function authenticateStaff($siteId, $username, $password)
    {
        if (! $this->validateSiteId($siteId)) {
            throw new PlatformGatewayException('Something went wrong while getting authentication token.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response = $this->validateLogin($username, $password);

        if (! $response) {
            throw new PlatformGatewayException('Authentication failed.', Response::HTTP_UNAUTHORIZED);
        }

        $staff = new Staff();
        $staff->api_id = data_get($response, 'User.Id');
        $staff->first_name = data_get($response, 'User.FirstName');
        $staff->last_name = data_get($response, 'User.LastName');
        $staff->api_role = strtolower(data_get($response, 'User.Type'));
        $staff->api_access_token = 'access-token';

        return $staff;
    }

    public function getStaffMember($siteId, $staffId)
    {
        return $this->getStaffMembers($siteId, [$staffId])->first();
    }

    public function getStaffMembers($siteId, array $staffIds = [])
    {
        if ($this->validateSiteId($siteId)) {
            if (empty($staffIds)) {
                $apiStaffCollection = $this->staff->where('siteId', $siteId);
            } else {
                $apiStaffCollection = $this->staff->filter(function ($staff) use ($siteId, $staffIds) {
                    return $staff['siteId'] === $siteId && in_array($staff['Id'], $staffIds);
                });
            }

            return $apiStaffCollection->filter(function ($staff) {
                return $staff['Id'] > 1;
            })->map(function ($apiStaff) {
                $staff = new Staff();
                $staff->api_id = data_get($apiStaff, 'Id');
                $staff->first_name = data_get($apiStaff, 'FirstName');
                $staff->last_name = data_get($apiStaff, 'LastName');
                $staff->api_access_token = null;

                return $staff;
            });
        }

        throw new PlatformGatewayException('The site id is not valid.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function getLocations($siteId)
    {
        if ($this->validateSiteId($siteId)) {
            $locations = $this->locations->where('SiteID', $siteId)->all();

            return collect($locations)->map(function ($apiLocation) {
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
        }

        throw new PlatformGatewayException('The site id is not valid.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function getClients($siteId)
    {
        if ($this->validateSiteId($siteId)) {
            $clients = $this->clients->where('SiteId', $siteId)->all();

            $propertyId = Property::findByApiIdentifier($siteId)->first()->id;

            $clients = collect($clients)->map(function ($apiClient) use ($propertyId) {
                $client = new Client();
                $client->property_id = $propertyId;
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
                $client->id = makeDoubleCompositeKey($propertyId, $client->api_id);

                return $client;
            });

            if (! is_null($this->skip)) {
                $total = $clients->count();

                $paginated = $clients->splice($this->skip) // Splits the collection at the index defined by skip.
                    ->values() // Resets all keys in array.
                    ->take($this->take); // Takes the correct amount of remaining items.

                $paginated->pagination = [
                    "RequestedLimit" => $this->take,
                    "RequestedOffset" => $this->skip,
                    "PageSize" => $paginated->count(),
                    "TotalResults" => $total,
                ];

                return $paginated;
            }

            return $clients;
        }

        throw new PlatformGatewayException('The site id is not valid.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function getSessionTypes($siteId)
    {
        if ($this->validateSiteId($siteId)) {
            $sessionTypes = $this->sessionTypes->where('siteId', $siteId)->all();

            return collect($sessionTypes)->pluck('Name', 'Id')->all();
        }

        throw new PlatformGatewayException('The site id is not valid.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function getMemberships($siteId)
    {
        try {
            $this->validateSiteId($siteId);

            $key = sprintf('%s:memberships', $siteId);

            return cache()->remember($key, now()->addHours(5), function () use ($siteId) {
                return collect([['id' => 3, 'name' => 'Red Membership'], ['id' => '202', 'name' => 'blue membership']]);
            });
        } catch (Exception $e) {
            throw new PlatformGatewayException('Something went wrong while getting memberships.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getAppointments($siteId, $startDate, $endDate, array $staffIds = [], string $clientId = null, $locationIds = [], $appointmentIds = [])
    {
        if ($this->validateSiteId($siteId)) {
            $startDate = $startDate ? Carbon::parse($startDate) : Carbon::today();
            $endDate = $endDate ? Carbon::parse($endDate) : Carbon::today();

            $apiAppointmentCollection = $this->appointments->filter(function ($appointment) use ($startDate, $endDate) {
                return Carbon::parse($appointment['StartDateTime']) >= $startDate && Carbon::parse($appointment['EndDateTime']) <= $endDate;
            })->filter(function ($appointment) use ($staffIds) {
                return count($staffIds) ? in_array($appointment['StaffId'], $staffIds) : true;
            })->filter(function ($appointment) use ($clientId) {
                return $clientId ? $appointment['ClientId'] === $clientId : true;
            })->filter(function ($appointment) use ($locationIds) {
                return count($locationIds) ? in_array($appointment['LocationId'], $locationIds) : true;
            })->filter(function ($appointment) use ($appointmentIds) {
                return count($appointmentIds) ? in_array($appointment['Id'], $appointmentIds) : true;
            });

            $property = Property::with('locations:id,api_id,property_id')->findByApiIdentifier($siteId)->first();
            $sessionTypes = $this->getSessionTypes($siteId);

            $appointments = $apiAppointmentCollection->map(function ($apiAppointment) use ($property, $sessionTypes) {
                return new Appointment([
                    'id' => makeTripleCompositeKey($property->id, data_get($apiAppointment, 'LocationId'), data_get($apiAppointment, 'Id')),
                    'api_id' => (string) data_get($apiAppointment, 'Id'),
                    'property_id' => $property->id,
                    'location_api_id' => data_get($apiAppointment, 'LocationId'),
                    'location_id' => $property->locations->where('api_id', data_get($apiAppointment, 'LocationId'))->first()->id,
                    'client_api_public_id' => data_get($apiAppointment, 'ClientId'),
                    'staff_api_id' => data_get($apiAppointment, 'StaffId'),
                    'staff_id' => makeDoubleCompositeKey($property->id, data_get($apiAppointment, 'StaffId')),
                    'duration' => data_get($apiAppointment, 'Duration'),
                    'status' => data_get($apiAppointment, 'Status'),
                    'start_date_time' => Carbon::parse(data_get($apiAppointment, 'StartDateTime')),
                    'end_date_time' => Carbon::parse(data_get($apiAppointment, 'EndDateTime')),
                    'notes' => data_get($apiAppointment, 'Notes'),
                    'staff_requested' => data_get($apiAppointment, 'StaffRequested'),
                    'service_id' => data_get($apiAppointment['SessionTypeId'], null),
                    'service_name' => data_get($sessionTypes, $apiAppointment['SessionTypeId'], 'Unknown Visit Type'),
                    'room_name' => $this->getRoomName($apiAppointment),
                    'first_appointment' => data_get($apiAppointment, 'FirstAppointment'),
                ]);
            });

            if (! is_null($this->skip)) {
                $total = $appointments->count();

                $paginated = $appointments->splice($this->skip) // Splits the collection at the index defined by skip.
                ->values() // Resets all keys in array.
                ->take($this->take); // Takes the correct amount of remaining items.

                $paginated->pagination = [
                    "RequestedLimit" => $this->take,
                    "RequestedOffset" => $this->skip,
                    "PageSize" => $paginated->count(),
                    "TotalResults" => $total,
                ];

                return $paginated;
            }

            return $appointments;
        }

        throw new PlatformGatewayException('The site id is not valid.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function updateAppointment($siteId, $appointment)
    {
        $this->validateSiteId($siteId);

        $this->appointments = $this->appointments->map(function ($a) use ($appointment) {
            if ($a['Id'] == $appointment->api_id) {
                // Update the notes.
                $a['Notes'] = $appointment->notes;
            }

            return $a;
        });

        return $this->getAppointments($siteId, now()->parse('January 1st, 2000'), now(), [], null, [], [$appointment->api_id])->first();
    }

    public function withPagination($take, $skip)
    {
        $this->take = $take;
        $this->skip = $skip;

        return $this;
    }

    public function getClasses($siteId, Carbon $startDate = null, Carbon $endDate = null, $staffIds = [], $locationIds = [], $classIds = [])
    {
        if ($this->validateSiteId($siteId)) {
            $startDate = $startDate ? $startDate->startOfDay() : Carbon::today()->startOfDay();
            $endDate = $endDate ? $endDate->endOfDay() : Carbon::today()->endOfDay();
            $apiProviderClassesCollection = $this->providerClasses->filter(function ($providerClass) use ($startDate, $endDate, $staffIds, $locationIds, $classIds) {
                $checkCondition = Carbon::parse($providerClass['StartDateTime']) >= $startDate && Carbon::parse($providerClass['EndDateTime']) <= $endDate;
                if (! empty($staffIds)) {
                    $checkCondition = $checkCondition && in_array(data_get($providerClass, 'Staff.Id'), $staffIds);
                }
                if (! empty($locationIds)) {
                    $checkCondition = $checkCondition && in_array(data_get($providerClass, 'Location.Id'), $locationIds);
                }
                if (! empty($classIds)) {
                    $checkCondition = $checkCondition && in_array(data_get($providerClass, 'Location.Id'), $classIds);
                }
                return $checkCondition;
            });

            return $apiProviderClassesCollection->map(function ($apiClass) {
                $providerClass = new ProviderClass();

                $providerClass->is_canceled = data_get($apiClass, 'IsCanceled');
                $providerClass->api_id = data_get($apiClass, 'Id');
                $providerClass->api_instance_id = data_get($apiClass, 'ClassScheduleId');
                $providerClass->start_date_time = Carbon::parse(data_get($apiClass, 'StartDateTime'));
                $providerClass->end_date_time = Carbon::parse(data_get($apiClass, 'EndDateTime'));
                $providerClass->resource_name = data_get($apiClass, 'Resource.Name');
                $providerClass->clients = data_get($apiClass, 'Clients');
                $providerClass->class_description_name = data_get($apiClass, 'ClassDescription.Name');
                $providerClass->staff_api_id = data_get($apiClass, 'Staff.Id');
                $providerClass->location_api_id = data_get($apiClass, 'Location.Id');

                return $providerClass;
            });
        }

        throw new PlatformGatewayException('The site id is not valid.', Response::HTTP_UNPROCESSABLE_ENTITY);
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

    protected function validateLogin($username, $password)
    {
        if ($username === 'owner' && $password === env('OWNER_PASSWORD')) {
            return [
                'User' => [
                    'Id' => 1,
                    'FirstName' => 'owner',
                    'LastName' => 'owner',
                    'Type' => 'Owner',
                ],
            ];
        }

        if ($username === config('platform.mindbody.app_username')) {
            return [
                'User' => [
                    'Id' => 100000000,
                    'FirstName' => '_QuickernotesLLC',
                    'LastName' => 'API',
                    'Type' => 'Staff',
                ],
            ];
        } // All test passwords are @tempPW1234.
        elseif ($password === '@tempPW1234') {
            $responses = [
                'valid-test-owner' => [
                    'User' => [
                        'Id' => 1,
                        'FirstName' => 'owner-first',
                        'LastName' => 'owner-last',
                        'Type' => 'Owner',
                    ],
                ],
                'valid-test-staff' => [
                    'User' => [
                        'Id' => 100000005,
                        'FirstName' => 'valid',
                        'LastName' => 'test-staff',
                        'Type' => 'Staff',
                    ],
                ],
            ];

            return $responses[$username] ?? null;
        }
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
}
