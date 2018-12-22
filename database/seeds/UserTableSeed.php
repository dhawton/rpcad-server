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

        $character = new Character(json_decode(
            '{"firstname":"Marshall","lastname":"Eriksen","address":"Los Santos","gender":"Male","datebirth":"1986-03-06","race":"White","haircolor":"Brown","height_feet":6,"height_inches":8,"photo":"IA==","licensestatus":"Learner\'s Permit","idnumber":"H153-325-87-619-3","updated_at":"2018-08-03 01:12:41","created_at":"2018-08-03 01:12:41","id":1}',
            true
        ));
        $character->user_id = $user->id;
        $character->weight = 200;
        $character->save();
    }
}
