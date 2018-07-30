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
 *     @SWG\Property(property="id", type="integer", description="CAD User ID #"),
 *     @SWG\Property(property="name", type="string", description="Account name"),
 *     @SWG\Property(property="email", type="string", description="Email address of user"),
 *     @SWG\Property(property="created_at", type="string", description="Date/time added to database"),
 *     @SWG\Property(property="updated_at", type="string", description="Date/time last updated in database"),
 *     @SWG\Property(property="roles", type="array", description="Array of roles",
 *         @SWG\Items(type="object",
 *             @SWG\Property(property="role", type="string", description="Role")
 *         )
 *     )
 * )
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        "roles"
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
