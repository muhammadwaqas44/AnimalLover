<?php


use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Admin', 'User'];
        foreach($roles as $role){
            App\Role::create(['name' => $role]);
        }
    }
}
