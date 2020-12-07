<?php

use Illuminate\Database\Seeder;

class PostTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Ask a question','Post pictures of your pets','Find a pet sitter'];
        foreach($roles as $role){
            App\PostTopic::create(['topic' => $role]);
        }
    }
}
