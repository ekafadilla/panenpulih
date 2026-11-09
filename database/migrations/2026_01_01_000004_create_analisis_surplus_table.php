<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_surplus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_panen_id')->constrained('rencana_panen')->cascadeOnDelete();
            $table->decimal('potensi_surplus_kg', 10, 2);
            $table->decimal('persentase_surplus', 5, 2); // % of H
            $table->decimal('skor_persentase_surplus', 5, 2); // Sp
            $table->decimal('skor_waktu', 5, 2); // W
            $table->decimal('skor_kerentanan', 5, 2); // U
            $table->decimal('skor_penyimpanan', 5, 2); // G
            $table->decimal('skor_permintaan', 5, 2); // D
            $table->decimal('skor_risiko_total', 5, 2); // R = 0.25Sp + 0.20W + 0.25U + 0.15G + 0.15D
            $table->string('kategori_risiko'); // Rendah, Sedang, Tinggi, Sangat Tinggi
            $table->integer('hari_menuju_panen')->default(0);
            $table->text('rekomendasi_tindakan')->nullable();
            $table->text('catatan_analisis')->nullable();
            $table->date('tanggal_analisis');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_surplus');
    }
};
