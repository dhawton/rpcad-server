<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public static function add($userid, $role) {
        $r = new Role();
        $r->user_id = $userid;
        $r->role = $role;
        $r->save();
        return $r;
    }
}
