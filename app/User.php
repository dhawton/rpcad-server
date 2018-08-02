<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App
 *
 * @SWG\Definition(
 *     type="object",
 *     @SWG\Property(property="id", type="integer", description="CAD User ID #", example="1"),
 *     @SWG\Property(property="name", type="string", description="Account name", example="Daniel H."),
 *     @SWG\Property(property="email", type="string", description="Email address of user", example="daniel@hawton.org"),
 *     @SWG\Property(property="status", type="string", description="Current status [valid options: Offline, Available, Busy, Out of Service]", example="Offline"),
 *     @SWG\Property(property="server_id", type="integer", description="Server ID connected to, null for offline", example="1"),
 *     @SWG\Property(property="department", type="string", description="Department user is associated with [when connected, null when offline]", example="Sheriff"),
 *     @SWG\Property(property="division", type="string", description="Division user working in [example: K9]", example="K9"),
 *     @SWG\Property(property="signed_on", type="string", description="Date/Time user signed into CAD", example="2018-07-30 12:31:33"),
 *     @SWG\Property(property="created_at", type="string", description="Date/time added to database", example="2018-07-30 12:31:33"),
 *     @SWG\Property(property="updated_at", type="string", description="Date/time last updated in database", example="2018-07-30 12:31:33"),
 *     @SWG\Property(property="roles", type="array", description="Array of roles",
 *         @SWG\Items(type="object",
 *             @SWG\Property(property="role", type="string", description="Role", example="admin")
 *         )
 *     )
 * )
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'identifier', 'identifier_type'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        "roles"
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'signed_on'
    ];

    public function hasRole($role) {
        return !(null === $this->role()->where("role", $role)->first());
    }

    public function role() {
        return $this->hasOne("App\Role", "user_id", "id");
    }

    public function roles() {
        return $this->hasMany("App\Role", "user_id", "id");
    }

    public function getRolesAttribute() {
        $roles = [];

        foreach (Role::where('user_id', $this->id)->get() as $r) {
            $roles[] = $r->role;
        }

        return $roles;
    }
}
