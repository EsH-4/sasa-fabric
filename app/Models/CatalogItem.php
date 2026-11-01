<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class CatalogItem extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'description',
        'image_path',
        'media_type',
        'is_available',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Video file extensions supported for catalog uploads.
     */
    public const VIDEO_EXTENSIONS = ['mp4', 'webm', 'mov', 'avi'];

    /**
     * Video mime types accepted for upload validation.
     */
    public const VIDEO_MIMES = ['mp4', 'webm', 'mov', 'avi'];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'catalog_item_material');
    }

    /**
     * Determine whether this catalog item is a video.
     */
    public function isVideo(): bool
    {
        if ($this->media_type === 'video') {
            return true;
        }

        return in_array(strtolower(pathinfo((string) $this->image_path, PATHINFO_EXTENSION)), self::VIDEO_EXTENSIONS, true);
    }

    /**
     * Determine whether this catalog item is an image.
     */
    public function isImage(): bool
    {
        return ! $this->isVideo();
    }

    /**
     * Get the public URL for the media (image or video).
     */
    public function mediaUrl(): string
    {
        if (! $this->image_path) {
            return asset('images/logo.png');
        }

        return asset('storage/'.ltrim((string) $this->image_path, '/'));
    }

    /**
     * Alias kept for backward-compatibility.
     */
    public function imageUrl(): string
    {
        return $this->mediaUrl();
    }

    /**
     * Detect media type from a file extension.
     */
    public static function detectMediaType(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return in_array($ext, self::VIDEO_EXTENSIONS) ? 'video' : 'image';
    }

    public function waOrderUrl(?SiteSetting $setting = null): string
    {
        $setting = $setting ?? SiteSetting::current();

        $pesan = "Halo Sasa, saya ingin pesan/tanya kain *{$this->name}*";
        if ($this->code) {
            $pesan .= " (Kode/Komposisi: {$this->code})";
        }
        if ($this->price) {
            $pesan .= " [{$this->price}]";
        }
        $pesan .= ". Apakah masih bisa dipesan?";

        return $setting->waUrl($pesan);
    }
}
