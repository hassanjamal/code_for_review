<?php

namespace App\PlatformAPI;

use App\Exceptions\PlatformGatewayException;
use Illuminate\Support\Carbon;

interface PlatformGateway
{
    /**
     * @param $siteId
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function getActivationCode($siteId);

    /**
     * @param $siteId
     * @return mixed
     */
    public function authenticateApp($siteId);

    /**
     * @param $siteId
     * @param $username
     * @param $password
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function authenticateStaff($siteId, $username, $password);

    /**
     * @param $siteId
     * @param $staffId
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function getStaffMember($siteId, $staffId);

    /**
     * @param $siteId
     * @param array $staffIds
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function getStaffMembers($siteId, array $staffIds =[]);

    /**
     * @param $siteId
     * @return mixed
     * @throws PlatformGatewayException
     */
    public function getLocations($siteId);

    /**
     * @param $siteId
     * @return mixed
     */
    public function getClients($siteId);

    /**
     * @param $siteId
     * @return mixed
     */
    public function getSessionTypes($siteId);

    public function getAppointments($siteId, Carbon $startDate, Carbon $endDate, array $staffIds = [], string $clientIds = null, array $locationIds = [], array $appointmentIds = []);

    public function updateAppointment($siteId, $appointment);
}
