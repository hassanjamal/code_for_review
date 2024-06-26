<?php

use App\User;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') === 'local') {
            // Initialize the seeded tenant from SystemDatabaseSeeder.
            tenancy()->init('acme.qn2020.test');

            // Seed roles and permissions tables.
            $this->call(TenantRolesSeeder::class);

            // Create an admin user.
            $user = User::create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@acme.com',
                'password' => bcrypt('secret'),
            ]);
            $user->assignRole('Super Admin');
        } else {
            // Seed roles and permissions tables.
            $this->call(TenantRolesSeeder::class);
        }
    }
}
