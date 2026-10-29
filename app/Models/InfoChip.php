<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoChip extends Model
{
    protected $fillable = [
        'text',
        'sort_order',
    ];
}
