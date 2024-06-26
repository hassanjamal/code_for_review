<?php

use App\Console\Commands\MapsOverClasses;
use Illuminate\Database\Seeder;

class TenantRolesSeeder extends Seeder
{
    use MapsOverClasses;

    public function run()
    {
        // Determine if tenancy has been initialized.
        $tenant = tenant();

        if ($tenant) {
            Artisan::call('qn:sync-roles-and-permissions', ['--tenantId' => $tenant->id]);
        } else {
            Artisan::call('qn:sync-roles-and-permissions');
        }
    }
}
