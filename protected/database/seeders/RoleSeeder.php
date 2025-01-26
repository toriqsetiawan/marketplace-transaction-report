<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'administrator', 'description' => 'System administrator'],
            ['name' => 'admin', 'description' => 'Admins linked to stores'],
            ['name' => 'reseller', 'description' => 'Resellers linked to stores'],
            ['name' => 'customer', 'description' => 'Customers who buy products'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
