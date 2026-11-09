<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penawaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penawaran')->unique();
            $table->foreignId('rencana_panen_id')->constrained('rencana_panen')->cascadeOnDelete();
            $table->foreignId('kebutuhan_pembeli_id')->nullable()->constrained('kebutuhan_pembeli')->nullOnDelete();
            $table->foreignId('petani_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pembeli_id')->constrained('users')->cascadeOnDelete();
            $table->string('inisiator_role'); // petani, pedagang, umkm
            $table->decimal('jumlah_kg', 10, 2);
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('total_harga', 14, 2);
            $table->date('tanggal_pengambilan');
            $table->string('metode_pengiriman')->default('diambil_pembeli'); // diambil_pembeli, dikirim_petani, jasa_angkut
            $table->text('catatan')->nullable();
            $table->string('status')->default('menunggu_konfirmasi'); // menunggu_konfirmasi, disetujui, dinegosiasi, ditolak, dibatalkan, selesai
            $table->timestamps();
        });

        Schema::create('penyaluran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penyaluran')->unique();
            $table->foreignId('penawaran_id')->constrained('penawaran')->cascadeOnDelete();
            $table->date('tanggal_penyaluran');
            $table->decimal('jumlah_rencana_kg', 10, 2);
            $table->decimal('jumlah_realisasi_kg', 10, 2);
            $table->decimal('sisa_surplus_kg', 10, 2)->default(0);
            $table->decimal('tingkat_penyerapan_persen', 5, 2)->default(100.0);
            $table->decimal('harga_total_realisasi', 14, 2);
            $table->string('status_kualitas')->default('sesuai_ekspektasi'); // sesuai_ekspektasi, cacat_ringan, diolah_lanjut
            $table->string('bukti_penyaluran')->nullable();
            $table->text('catatan_penyaluran')->nullable();
            $table->string('status_verifikasi')->default('terverifikasi'); // menunggu_verifikasi, terverifikasi, selesai
            $table->integer('rating_pembeli')->nullable();
            $table->text('ulasan_pembeli')->nullable();
            $table->decimal('dampak_food_loss_terhindar_kg', 10, 2)->default(0);
            $table->decimal('dampak_nilai_ekonomi_rp', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyaluran');
        Schema::dropIfExists('penawaran');
    }
};
