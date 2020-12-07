<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                "email" => "admin@animallovers.com",
                "password" => bcrypt("12345"),
                "first_name" => "Animal lover",
                "last_name" => "Animal lover",
                "phone" => "03123769495",
                "status" => 1,
                "role_id" => 1,
                "package_id" => 1,
            ]
        ];

        foreach ($users as $user) {

            \App\User::create([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'phone' => $user['phone'],
                'status' => $user['status'],
                'role_id' => $user['role_id'],
                'package_id' => $user['package_id'],
            ]);
        }
    }
}
