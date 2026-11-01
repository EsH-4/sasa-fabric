<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class GalleryPhoto extends Model
{
    protected $fillable = [
        'material_id',
        'path',
        'caption',
        'sort_order',
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'gallery_photo_material');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function url(): string
    {
        return asset('storage/'.ltrim((string) $this->path, '/'));
    }
}
