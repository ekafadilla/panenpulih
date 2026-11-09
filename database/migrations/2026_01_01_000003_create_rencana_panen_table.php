<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rencana_panen', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rencana')->unique();
            $table->foreignId('petani_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('komoditas_id')->constrained('komoditas')->cascadeOnDelete();
            $table->decimal('luas_lahan_m2', 10, 2)->default(1000);
            $table->decimal('estimasi_hasil_kg', 10, 2); // H
            $table->decimal('jumlah_terpesan_kg', 10, 2)->default(0); // P
            $table->decimal('jumlah_alokasi_lain_kg', 10, 2)->default(0); // K
            $table->decimal('potensi_surplus_kg', 10, 2)->default(0); // S = H - P - K
            $table->date('tanggal_tanam')->nullable();
            $table->date('tanggal_perkiraan_panen');
            $table->date('tanggal_panen_aktual')->nullable();
            $table->decimal('hasil_aktual_kg', 10, 2)->nullable();
            $table->string('lokasi');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('kondisi_tanaman')->default('baik'); // sangat_baik, baik, sedang, terdampak_cuaca
            $table->string('grade')->default('grade_a'); // grade_a, grade_b, imperfect, campur
            $table->string('fasilitas_penyimpanan')->default('shaded_shed'); // open_air, shaded_shed, ventilated_warehouse, cold_storage
            $table->decimal('ekspektasi_harga_per_kg', 12, 2)->nullable();
            $table->string('foto')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status')->default('aktif'); // draft, aktif, panen, selesai, dibatalkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rencana_panen');
    }
};
