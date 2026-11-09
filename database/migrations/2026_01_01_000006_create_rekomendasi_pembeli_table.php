<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekomendasi_pembeli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_panen_id')->constrained('rencana_panen')->cascadeOnDelete();
            $table->foreignId('kebutuhan_pembeli_id')->constrained('kebutuhan_pembeli')->cascadeOnDelete();
            $table->decimal('skor_total', 5, 2); // M = 0.30C + 0.20Q + 0.20L + 0.20T + 0.10J
            $table->decimal('skor_komoditas', 5, 2); // C
            $table->decimal('skor_kuantitas', 5, 2); // Q
            $table->decimal('skor_lokasi', 5, 2); // L
            $table->decimal('skor_waktu', 5, 2); // T
            $table->decimal('skor_kualitas', 5, 2); // J
            $table->decimal('jarak_km', 8, 2)->default(0);
            $table->text('alasan_rekomendasi')->nullable();
            $table->string('status')->default('rekomendasi'); // rekomendasi, ditawarkan, disepakati, ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_pembeli');
    }
};
