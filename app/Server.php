<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Server
 * @package App
 *
 * @SWG\Definition(
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="name", type="string", description="Unique server name"),
 *     @SWG\Property(property="created_at", type="string", description="Date added to database"),
 *     @SWG\Property(property="updated_at", type="string"),
 * )
 */
class Server extends Model
{
    protected $fillable = ['name'];
}
