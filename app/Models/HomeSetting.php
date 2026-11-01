<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSetting extends Model
{
    protected $fillable = [
        'judul_bestseller',
    ];

    public static function current(): self
    {
        try {
            return static::query()->first() ?? static::query()->create(['judul_bestseller' => 'Bestseller']);
        } catch (\Throwable $e) {
            $instance = new static(['judul_bestseller' => 'Bestseller']);
            return $instance;
        }
    }
}
