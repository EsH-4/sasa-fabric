<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    protected $fillable = [
        'name',
        'sort_order',
    ];

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(GalleryPhoto::class, 'gallery_photo_material');
    }

    public function catalogItems(): BelongsToMany
    {
        return $this->belongsToMany(CatalogItem::class, 'catalog_item_material');
    }
}
