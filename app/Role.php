<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package App
 *
 * @SWG\Definition(
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="user_id", type="integer", description="User Identification Number"),
 *     @SWG\Property(property="role", type="string", description="Associated Role"),
 *     @SWG\Property(property="created_at", type="string", description="Date added to database"),
 *     @SWG\Property(property="updated_at", type="string"),
 * )
 */
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
