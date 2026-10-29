<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // Kode Kain
            $table->text('composition')->nullable(); // Kandungan Bahan
            $table->string('width')->nullable(); // Lebar
            $table->string('weight_gsm')->nullable(); // Berat / GSM
            $table->string('roll_estimate')->nullable(); // Estimasi Meter / Roll
            $table->decimal('price_ecer', 12, 2)->nullable(); // Harga Jual ECER
            $table->decimal('price_min_2_rol', 12, 2)->nullable(); // Harga Jual MIN 2 ROL
            $table->decimal('price_min_8_rol', 12, 2)->nullable(); // Harga Jual MIN 8 ROL
            $table->text('notes')->nullable(); // Catatan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};
