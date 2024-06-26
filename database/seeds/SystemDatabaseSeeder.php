<?php

use Illuminate\Database\Seeder;
use Stancl\Tenancy\Tenant;

class SystemDatabaseSeeder extends Seeder
{
    public function run()
    {
        Tenant::create(['acme.qn2020.test'], [
            'name' => 'Acme, Inc.',
            'phone' => '444-444-4444',
            'email' => 'test@acme.com',
            'stripe_id' => 'cus_Gjb72MUDUYT0v6',
        ]);
    }
}
