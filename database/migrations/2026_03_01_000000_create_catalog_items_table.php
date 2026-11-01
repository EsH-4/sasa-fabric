<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
                $table->string('judul_katalog')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_item_material');
        Schema::dropIfExists('catalog_items');

        if (Schema::hasTable('site_settings') && Schema::hasColumn('site_settings', 'judul_katalog')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropColumn('judul_katalog');
            });
        }
    }
};
