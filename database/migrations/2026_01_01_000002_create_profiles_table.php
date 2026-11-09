<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petani', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_poktan')->nullable();
            $table->decimal('luas_lahan_ha', 8, 2)->default(1.0);
            $table->text('alamat_lahan')->nullable();
            $table->string('fasilitas_penyimpanan')->default('open_air'); // none, open_air, shaded_shed, ventilated_warehouse, cold_storage
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('kontak_darurat')->nullable();
            $table->timestamps();
        });

        Schema::create('pembeli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_usaha');
            $table->string('jenis_usaha'); // pedagang_pasar, distributor_besar, umkm_olahan, industri_makanan, food_bank
            $table->decimal('kapasitas_serap_mingguan_kg', 10, 2)->default(500);
            $table->string('preferensi_grade')->default('semua'); // semua, grade_a, grade_b, imperfect
            $table->text('alamat_gudang')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('deskripsi_kebutuhan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembeli');
        Schema::dropIfExists('petani');
    }
};
