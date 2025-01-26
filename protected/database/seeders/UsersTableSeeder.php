<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'role_id'    => 1,
                'name'    => 'Administrator',
                'email'    => 'toriqbagus@gmail.com',
                'password'    => bcrypt('Hades.unit1')
            ],
            [
                'role_id'    => 2,
                'name'    => 'Admin',
                'email'    => 'sales.artfootwear@gmail.com',
                'password'    => bcrypt('Hades.123')
            ],
            [
                'role_id'    => 3,
                'name'    => 'Reseller DR.FOE',
                'email'    => 'reseller.drfoe@gmail.com',
                'password'    => bcrypt('Reseller.123')
            ],
            [
                'role_id'    => 3,
                'name'    => 'Reseller Menside',
                'email'    => 'reseller.menside@gmail.com',
                'password'    => bcrypt('Reseller.123')
            ],
            [
                'role_id'    => 3,
                'name'    => 'Reseller Ahong',
                'email'    => 'reseller.ahong@gmail.com',
                'password'    => bcrypt('Reseller.123')
            ],
            [
                'role_id'    => 3,
                'name'    => 'Reseller Mala',
                'email'    => 'reseller.mala@gmail.com',
                'password'    => bcrypt('Reseller.123')
            ],
            [
                'role_id'    => 4,
                'name'    => 'Shopee Art Footwear',
                'email'    => 'artffootwear.shopee@gmail.com',
                'password'    => bcrypt('Customer.123')
            ],
            [
                'role_id'    => 4,
                'name'    => 'Tokopedia Art Footwear',
                'email'    => 'artffootwear.tokopedia@gmail.com',
                'password'    => bcrypt('Customer.123')
            ],
            [
                'role_id'    => 4,
                'name'    => 'Lazada Art Footwear',
                'email'    => 'artffootwear.lazada@gmail.com',
                'password'    => bcrypt('Customer.123')
            ],
            [
                'role_id'    => 4,
                'name'    => 'Tiktok Art Footwear',
                'email'    => 'artffootwear.tiktok@gmail.com',
                'password'    => bcrypt('Customer.123')
            ],
            [
                'role_id'    => 4,
                'name'    => 'Shopee Excel',
                'email'    => 'excel.shopee@gmail.com',
                'password'    => bcrypt('Customer.123')
            ],
            [
                'role_id'    => 4,
                'name'    => 'Shopee Black Edition',
                'email'    => 'blackedition.shopee@gmail.com',
                'password'    => bcrypt('Customer.123')
            ],
            [
                'role_id'    => 4,
                'name'    => 'Shopee Hafiz Sport',
                'email'    => 'hafizsport.shopee@gmail.com',
                'password'    => bcrypt('Customer.123')
            ],
            [
                'role_id'    => 4,
                'name'    => 'Shopee Tosant',
                'email'    => 'tosant.shopee@gmail.com',
                'password'    => bcrypt('Customer.123')
            ]
        ];

        foreach ($data as $user) {
            User::create($user);
        }
    }
}
