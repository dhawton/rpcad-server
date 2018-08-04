<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Character
 * @package App
 *
 * @SWG\Definition(
 *     type="object",
 *     @SWG\Property(property="id", type="integer", description="CAD Character ID #", example="1"),
 *     @SWG\Property(property="user_id", type="integer", description="CAD User #", example="1"),
 *     @SWG\Property(property="idnumber", type="string", description="Generated driver's license/ID card number", example="H123-123-12-123-1"),
 *     @SWG\Property(property="firstname", type="string", description="Character's first name", example="John"),
 *     @SWG\Property(property="lastname", type="string", description="Character's last name", example="Doe"),
 *     @SWG\Property(property="address", type="string", description="Street address (98 New Empire Way)", example="98 New Empire Way"),
 *     @SWG\Property(property="city", type="string", description="City name (Los Santos, Harmony, Sandy Shores, Paleto, Grapeseed, Chumash)", example="Los Santos"),
 *     @SWG\Property(property="gender", type="string", description="Gender (male or female)", example="Male"),
 *     @SWG\Property(property="datebirth", type="string", description="Date of Birth (YYYY-MM-DD)", example="1986-03-06"),
 *     @SWG\Property(property="race", type="string", description="Race", example="White"),
 *     @SWG\Property(property="haircolor", type="string", description="Hair color", example="Brown"),
 *     @SWG\Property(property="height_feet", type="integer", description="Height in feet", example="6"),
 *     @SWG\Property(property="height_inches", type="integer", description="Height, inches field", example="1"),
 *     @SWG\Property(property="weight", type="integer", description="Weight", example="187"),
 *     @SWG\Property(property="photo", type="string", description="Coming soon."),
 *     @SWG\Property(property="licensestatus", type="string", description="License status (ID Only, Learner's Permit, Valid, Suspended, Reokved)", example="Valid"),
 *     @SWG\Property(property="created_at", type="string", description="Date time created"),
 *     @SWG\Property(property="updated_at", type="string", description="Date time updated"),
 * )
 */
class Character extends Model
{
    protected $fillable = [
        "firstname", "lastname", "address", "gender", "datebirth", "race", "haircolor", "height_feet",
        "height_inches", "photo", "licensestatus"
    ];

    public static function boot() {
        parent::boot();

        self::creating(function($model) {
            $model->idnumber = $model->generateIDNumber();
        });
    }

    public function generateIDNumber() {
        // Format: Letter - 3 - 3 - 2 - 3 - 1
        $letters = [
            '' , 'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L', 'M'
        ];
        preg_match("/^(\d\d\d)(\d\d\d)(\d\d)(\d\d\d)(\d)/", microtime(true) * 100, $matches);
        return $letters[ date("n") ] . $matches[1] . "-" . $matches[2] . "-" . $matches[3] . "-" .
            $matches[4] . "-" . $matches[5];
    }

    public function user() {
        return $this->hasOne("App\User","id", "user_id");
    }
}
