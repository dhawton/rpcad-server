<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
