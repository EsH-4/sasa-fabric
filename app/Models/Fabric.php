<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabric extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'composition',
        'width',
        'weight_gsm',
        'roll_estimate',
        'price_ecer',
        'price_min_2_rol',
        'price_min_8_rol',
        'notes',
    ];
}
