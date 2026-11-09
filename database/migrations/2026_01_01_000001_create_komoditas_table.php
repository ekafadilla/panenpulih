<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komoditas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_latin')->nullable();
            $table->string('kategori'); // sayuran, buah, palawija, rempah
            $table->integer('umur_simpan_hari')->default(7);
            $table->integer('skor_kerentanan')->default(70); // 0-100 (U indikator)
            $table->string('satuan')->default('kg');
            $table->decimal('harga_acuan_per_kg', 12, 2)->default(15000);
            $table->text('deskripsi')->nullable();
            $table->text('pedoman_penyimpanan')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komoditas');
    }
};
