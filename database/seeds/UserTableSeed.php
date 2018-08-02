<?php

use Illuminate\Database\Seeder;

class UserTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\User();
        $user->password = \Hash::make('test1234');
        $user->identifier = 315;
        $user->identifier_type = "LEO";
        $user->name = "Daniel H.";
        $user->email = "daniel@hawton.org";
        $user->active = 1;
        $user->save();

        \App\Role::add($user->id, "Admin");
        \App\Role::add($user->id, "Dispatch");
        \App\Role::add($user->id, "Sheriff");
        \App\Role::add($user->id, "Police");
        \App\Role::add($user->id, "Highway");
        \App\Role::add($user->id, "Civilian");

        $s = new \App\Server(['name' => 'Test Server']);
        $s->save();
    }
}
