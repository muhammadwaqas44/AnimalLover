<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(PackageTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ServiceNames::class);
        $this->call(PostTopicSeeder::class);
        $this->call(UserTableSeeder::class);
    }
}
