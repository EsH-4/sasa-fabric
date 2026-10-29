<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();
            $table->string('headline')->nullable();
            $table->text('subjudul')->nullable();
            $table->string('wa')->nullable();
            $table->string('wa_tampil')->nullable();
            $table->string('ig')->nullable();
            $table->string('katalog')->nullable();
            $table->text('pesan')->nullable();
            $table->string('tombol1')->nullable();
            $table->string('tombol2')->nullable();
            $table->string('judul_galeri')->nullable();
            $table->string('judul_koleksi')->nullable();
            $table->string('judul_cara')->nullable();
            $table->string('judul_sk')->nullable();
            $table->text('footer')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
