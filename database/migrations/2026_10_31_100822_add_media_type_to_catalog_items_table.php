<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('catalog_items', 'media_type')) {
            Schema::table('catalog_items', function (Blueprint $table) {
                $table->string('media_type', 10)->default('image')->after('image_path');
            });
        }

        // Set existing rows: default to 'image', detect video by extension
        DB::table('catalog_items')
            ->whereNull('media_type')
            ->orWhere('media_type', '')
            ->update(['media_type' => 'image']);

        DB::table('catalog_items')
            ->where(function ($q) {
                $q->where('image_path', 'like', '%.mp4')
                  ->orWhere('image_path', 'like', '%.webm')
                  ->orWhere('image_path', 'like', '%.mov')
                  ->orWhere('image_path', 'like', '%.avi');
            })
            ->update(['media_type' => 'video']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('catalog_items', 'media_type')) {
            Schema::table('catalog_items', function (Blueprint $table) {
                $table->dropColumn('media_type');
            });
        }
    }
};
