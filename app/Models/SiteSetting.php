<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'judul',
        'headline',
        'subjudul',
        'wa',
        'wa_tampil',
        'ig',
        'katalog',
        'pesan',
        'tombol1',
        'tombol2',
        'judul_galeri',
        'judul_katalog',
        'judul_koleksi',
        'judul_cara',
        'judul_sk',
        'footer',
        'logo_path',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? static::query()->create([]);
    }

    public function logoUrl(): ?string
    {
        if ($this->logo_path) {
            $storagePath = public_path('storage/'.ltrim((string) $this->logo_path, '/'));
            if (file_exists($storagePath)) {
                return asset('storage/'.ltrim((string) $this->logo_path, '/'));
            }
        }

        if (file_exists(public_path('images/logo.png'))) {
            return asset('images/logo.png');
        }

        if ($this->logo_path) {
            return asset('storage/'.ltrim((string) $this->logo_path, '/'));
        }

        return null;
    }

    public function katalogUrl(): string
    {
        if ($this->katalog) {
            return $this->katalog;
        }

        $clean = preg_replace('/\D/', '', (string) $this->wa);
        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        }

        return 'https://wa.me/c/'.$clean;
    }

    public function waUrl(?string $text = null): string
    {
        $clean = preg_replace('/\D/', '', (string) $this->wa);
        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        }

        $url = 'https://wa.me/'.$clean;

        if ($text) {
            $url .= '?text='.rawurlencode($text);
        }

        return $url;
    }

    public function getWaTampilAttribute($value): string
    {
        if (! empty($value)) {
            return $value;
        }

        if (! $this->wa) {
            return '';
        }

        $clean = preg_replace('/\D/', '', (string) $this->wa);
        if (str_starts_with($clean, '62')) {
            return '0'.substr($clean, 2);
        }

        return $clean;
    }
}
