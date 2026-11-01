<?php

namespace App\Providers;

use App\Models\CatalogItem;
use App\Models\GalleryPhoto;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure default asset images exist in storage
        try {
            $assetBase = database_path('seeders/assets');
            if (File::isDirectory($assetBase)) {
                if (File::exists($assetBase.'/logo.png') && ! Storage::disk('public')->exists('logo.png')) {
                    Storage::disk('public')->put('logo.png', File::get($assetBase.'/logo.png'));
                }
                foreach (['01.jpg', '02.jpg', '03.jpg', '04.jpg'] as $img) {
                    $src = $assetBase.'/gallery/'.$img;
                    if (File::exists($src) && ! Storage::disk('public')->exists('gallery/'.$img)) {
                        Storage::disk('public')->put('gallery/'.$img, File::get($src));
                    }
                }
            }
        } catch (\Throwable $e) {
            //
        }

        // Auto-create catalog tables if not exist (runtime migration fallback)
        try {
            if (! Schema::hasTable('catalog_items')) {
                Schema::create('catalog_items', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('code')->nullable();
                    $table->string('price')->nullable();
                    $table->text('description')->nullable();
                    $table->string('image_path');
                    $table->boolean('is_available')->default(true);
                    $table->boolean('is_featured')->default(false);
                    $table->integer('sort_order')->default(0);
                    $table->timestamps();
                });
            }

            if (! Schema::hasTable('catalog_item_material')) {
                Schema::create('catalog_item_material', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('catalog_item_id')->constrained('catalog_items')->cascadeOnDelete();
                    $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
                    $table->timestamps();
                });
            }

            if (Schema::hasTable('site_settings') && ! Schema::hasColumn('site_settings', 'judul_katalog')) {
                Schema::table('site_settings', function (Blueprint $table) {
                    $table->string('judul_katalog')->nullable()->default('Katalog Koleksi Kain');
                });
            }

            if (! Schema::hasTable('home_settings')) {
                Schema::create('home_settings', function (Blueprint $table) {
                    $table->id();
                    $table->string('judul_bestseller')->default('Bestseller');
                    $table->timestamps();
                });
            }

            // Auto-populate from gallery_photos if catalog is currently empty
            if (Schema::hasTable('catalog_items') && CatalogItem::query()->count() === 0 && Schema::hasTable('gallery_photos')) {
                $photos = GalleryPhoto::with('materials')->orderBy('sort_order')->get();
                foreach ($photos as $i => $photo) {
                    $item = CatalogItem::create([
                        'name' => $photo->caption ?: 'Kain Sasa #'.($i + 1),
                        'code' => $photo->caption ? 'SF-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT) : null,
                        'price' => 'Sistem PO',
                        'description' => 'Koleksi kain pilihan Sasa Fabric dengan tekstur premium dan kenyamanan optimal.',
                        'image_path' => $photo->path,
                        'is_available' => true,
                        'is_featured' => true,
                        'sort_order' => $photo->sort_order,
                    ]);

                    $mIds = $photo->materials->pluck('id')->toArray();
                    if ($photo->material_id && ! in_array($photo->material_id, $mIds)) {
                        $mIds[] = $photo->material_id;
                    }

                    if (! empty($mIds)) {
                        $item->materials()->sync($mIds);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if DB connection not ready yet during setup
        }
    }
}
