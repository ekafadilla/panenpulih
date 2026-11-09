<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebutuhan_pembeli', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kebutuhan')->unique();
            $table->foreignId('pembeli_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('komoditas_id')->constrained('komoditas')->cascadeOnDelete();
            $table->decimal('jumlah_kg', 10, 2);
            $table->decimal('jumlah_terpenuhi_kg', 10, 2)->default(0);
            $table->date('tanggal_mulai_butuh');
            $table->date('batas_tanggal_butuh');
            $table->string('grade_diterima')->default('semua'); // semua, grade_a, grade_b, imperfect
            $table->decimal('budget_per_kg', 12, 2)->nullable();
            $table->string('lokasi');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('tujuan_penggunaan')->default('pasar_tradisional'); // pasar_tradisional, supermarket, olahan_makanan, restoran, food_bank
            $table->text('spesifikasi')->nullable();
            $table->string('status')->default('aktif'); // aktif, terpenuhi, dibatalkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_pembeli');
    }
};
