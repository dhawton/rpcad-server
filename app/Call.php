<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    public static function boot() {
        parent::boot();

        self::creating(function($model) {
            $model->id = $model->generateIDNumber();
        });
    }

    public function generateIDNumber() {
        // Format: 18-FZ091921
        // 18 = last 2 of year
        // F = month coded A (Jan) to L (Dec)
        // Z = day coded 0 (1st) through W (31st)
        // Rest is HHMMSS in 24 hour format
        $months = [
            '', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'
        ];
        $day = [
            '', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'M',
            'N', 'M', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'
        ];

        $p1 = date("y");
        $p2 = $months[ date("n") ];
        $p3 = $day[ date("j") ];
        $p4 = date("His");

        return $p1 . "-" . $p2 . $p3 . $p4;
    }
}
