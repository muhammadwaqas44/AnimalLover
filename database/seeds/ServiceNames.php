<?php

use App\InterestedService;
use Illuminate\Database\Seeder;

class ServiceNames extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role=[
            [
                'name'=>'Dating'
            ],
            [
                'name'=>'Dating_Socializing'
            ],
            [
                'name'=>'Socializing'
            ]
        ];

        foreach($role as $roles)
        {
            InterestedService::create($roles);
        }
    }
}
