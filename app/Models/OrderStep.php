<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStep extends Model
{
    protected $fillable = [
        'text',
        'sort_order',
    ];
}
