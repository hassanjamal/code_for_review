<?php

namespace App\Actions;

use App\Jobs\AddAppointmentsToDatabase;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use Illuminate\Support\Carbon;
use Spatie\QueueableAction\QueueableAction;

class CreateAppointmentFromMindbodyWebhookAction
{
    use QueueableAction;

    /**
     * @var \App\PlatformAPI\PlatformGateway
     */
    private $platformGateway;

    public function __construct(PlatformGateway $platformGateway)
    {
        $this->platformGateway = $platformGateway;
    }

    public function execute($data)
    {
        $siteId = data_get($data, "siteId");

        $tenant = tenancy()->all()->filter(function ($tenant) use ($siteId) {
            return array_key_exists('mb:'.$siteId, data_get($tenant, 'data'));
        })->first();

        if ($tenant) {
            tenancy()->initialize($tenant);

            $property = Property::findByApiIdentifier($siteId)->firstOrFail();

            $appointments = $this->platformGateway
                ->getAppointments(
                    $siteId,
                    Carbon::parse(data_get($data, "startDateTime")),
                    Carbon::parse(data_get($data, "endDateTime")),
                    [],
                    null,
                    [],
                    [data_get($data, "appointmentId")]
                );

            AddAppointmentsToDatabase::dispatchNow($appointments, $property);
        }
    }
}
