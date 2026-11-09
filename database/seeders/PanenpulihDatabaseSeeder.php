<?php

namespace Database\Seeders;

use App\Models\AnalisisSurplus;
use App\Models\KebutuhanPembeli;
use App\Models\Komoditas;
use App\Models\PembeliProfile;
use App\Models\Penawaran;
use App\Models\Penyaluran;
use App\Models\PetaniProfile;
use App\Models\RekomendasiPembeli;
use App\Models\RencanaPanen;
use App\Models\RiwayatStatus;
use App\Models\User;
use App\Services\BuyerMatchingService;
use App\Services\SurplusIntelligenceService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PanenpulihDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Komoditas Pertanian
        $komoditasData = [
            [
                'nama' => 'Cabai Merah Keriting',
                'nama_latin' => 'Capsicum annuum L.',
                'kategori' => 'sayuran',
                'umur_simpan_hari' => 5,
                'skor_kerentanan' => 85,
                'satuan' => 'kg',
                'harga_acuan_per_kg' => 35000,
                'deskripsi' => 'Komoditas bumbu utama bernilai ekonomi tinggi namun sangat mudah mengalami pembusukan dan penyusutan bobot jika terlambat dipasarkan.',
                'pedoman_penyimpanan' => 'Simpan di ruangan berventilasi dengan suhu 10-12°C. Hindari paparan sinar matahari langsung dan kelembapan jenuh.',
                'gambar' => 'cabai.jpg',
            ],
            [
                'nama' => 'Tomat Sayur / Cerutu',
                'nama_latin' => 'Solanum lycopersicum',
                'kategori' => 'sayuran',
                'umur_simpan_hari' => 6,
                'skor_kerentanan' => 88,
                'satuan' => 'kg',
                'harga_acuan_per_kg' => 12000,
                'deskripsi' => 'Kadar air tinggi menyebabkan laju respirasi cepat dan kerentanan tinggi terhadap benturan mekanis saat transportasi.',
                'pedoman_penyimpanan' => 'Gunakan peti kayu/keranjang plastik beralas daun pisang atau kertas. Jangan ditumpuk lebih dari 3 lapis.',
                'gambar' => 'tomat.jpg',
            ],
            [
                'nama' => 'Bawang Merah Brebes',
                'nama_latin' => 'Allium cepa var. aggregatum',
                'kategori' => 'rempah',
                'umur_simpan_hari' => 30,
                'skor_kerentanan' => 40,
                'satuan' => 'kg',
                'harga_acuan_per_kg' => 38000,
                'deskripsi' => 'Rempah esensial Indonesia dengan daya simpan lebih baik asalkan dikeringkan (pelayuan) dengan sempurna di para-para bambu.',
                'pedoman_penyimpanan' => 'Gantung di atas para-para berventilasi kering atau gudang bertingkat tradisional (Instore drying).',
                'gambar' => 'bawang.jpg',
            ],
            [
                'nama' => 'Manggis Wanayasa',
                'nama_latin' => 'Garcinia mangostana',
                'kategori' => 'buah',
                'umur_simpan_hari' => 10,
                'skor_kerentanan' => 75,
                'satuan' => 'kg',
                'harga_acuan_per_kg' => 28000,
                'deskripsi' => 'Buah eksotis khas Jawa Barat. Pengurangan kehilangan pascapanen bergantung pada kematangan panen dan penanganan getah kuning.',
                'pedoman_penyimpanan' => 'Suhu ruang 13°C dengan kemasan karton bersekat untuk mencegah benturan dan pengerasan kulit.',
                'gambar' => 'manggis.jpg',
            ],
            [
                'nama' => 'Jagung Manis',
                'nama_latin' => 'Zea mays saccharata',
                'kategori' => 'palawija',
                'umur_simpan_hari' => 8,
                'skor_kerentanan' => 55,
                'satuan' => 'kg',
                'harga_acuan_per_kg' => 9000,
                'deskripsi' => 'Kandungan gula cepat berubah menjadi pati setelah dipanen. Memerlukan penyerapan cepat oleh pasar atau industri olahan.',
                'pedoman_penyimpanan' => 'Biarkan kelobot tetap membungkus tongkol untuk menjaga kelembapan alami.',
                'gambar' => 'jagung.jpg',
            ],
            [
                'nama' => 'Kentang Granola Dieng',
                'nama_latin' => 'Solanum tuberosum',
                'kategori' => 'sayuran',
                'umur_simpan_hari' => 45,
                'skor_kerentanan' => 25,
                'satuan' => 'kg',
                'harga_acuan_per_kg' => 16500,
                'deskripsi' => 'Daya tahan simpan relatif tinggi di tempat gelap dan sejuk sebelum mata tunas tumbuh.',
                'pedoman_penyimpanan' => 'Simpan di ruang gelap tidak terpapar sinar lampu untuk mencegah solanin hijau.',
                'gambar' => 'kentang.jpg',
            ],
        ];

        $komoditasMap = [];
        foreach ($komoditasData as $item) {
            $k = Komoditas::create($item);
            $komoditasMap[$k->nama] = $k;
        }

        // 2. Akun Pengguna & Profil (Admin, Petani, Pedagang, UMKM)
        $password = Hash::make('password');

        // ADMIN
        $admin = User::create([
            'name' => 'Dr. Rahmat Hidayat (Bappenas / Tim Pangan)',
            'email' => 'admin@panenpulih.id',
            'password' => $password,
            'role' => 'admin',
            'phone' => '081122334455',
            'address' => 'Jl. Taman Suropati No. 2, Menteng, Jakarta Pusat',
            'institution' => 'Bappenas & Tim Satgas Food Loss PANENPULIH',
            'latitude' => -6.1994,
            'longitude' => 106.8326,
            'is_verified' => true,
        ]);

        // PETANI 1: Pak Supardi (Indramayu)
        $petani1 = User::create([
            'name' => 'Pak Supardi',
            'email' => 'petani@panenpulih.id',
            'password' => $password,
            'role' => 'petani',
            'phone' => '081234567890',
            'address' => 'Desa Cantigi Kulon, Kec. Cantigi, Kab. Indramayu',
            'institution' => 'Kelompok Tani Subur Makmur Indramayu',
            'latitude' => -6.3264,
            'longitude' => 108.3200,
            'is_verified' => true,
        ]);

        PetaniProfile::create([
            'user_id' => $petani1->id,
            'nama_poktan' => 'Poktan Subur Makmur Cantigi',
            'luas_lahan_ha' => 2.5,
            'alamat_lahan' => 'Hamparan Sawah Blok Gandu, Cantigi Kulon, Indramayu',
            'fasilitas_penyimpanan' => 'shaded_shed',
            'latitude' => -6.3264,
            'longitude' => 108.3200,
            'kontak_darurat' => '081234567890',
        ]);

        // PETANI 2: Ibu Siti Aminah (Ciwidey Bandung)
        $petani2 = User::create([
            'name' => 'Ibu Siti Aminah',
            'email' => 'siti@panenpulih.id',
            'password' => $password,
            'role' => 'petani',
            'phone' => '081398765432',
            'address' => 'Desa Sugihmukti, Kec. Pasirjambu - Ciwidey, Kab. Bandung',
            'institution' => 'Gapoktan Barokah Pasirjambu',
            'latitude' => -7.0945,
            'longitude' => 107.5020,
            'is_verified' => true,
        ]);

        PetaniProfile::create([
            'user_id' => $petani2->id,
            'nama_poktan' => 'Gapoktan Barokah Pasirjambu',
            'luas_lahan_ha' => 1.8,
            'alamat_lahan' => 'Kebun Sayur Lereng Patuha, Pasirjambu',
            'fasilitas_penyimpanan' => 'ventilated_warehouse',
            'latitude' => -7.0945,
            'longitude' => 107.5020,
            'kontak_darurat' => '081398765432',
        ]);

        // PEDAGANG: H. Hendra Kusuma
        $pedagang1 = User::create([
            'name' => 'H. Hendra Kusuma',
            'email' => 'pedagang@panenpulih.id',
            'password' => $password,
            'role' => 'pedagang',
            'phone' => '081755566677',
            'address' => 'Jl. Raya Fatahillah No. 45, Plered, Cirebon (Akses Pantura)',
            'institution' => 'CV Agro Niaga Pantura Gemilang',
            'latitude' => -6.7120,
            'longitude' => 108.5123,
            'is_verified' => true,
        ]);

        PembeliProfile::create([
            'user_id' => $pedagang1->id,
            'nama_usaha' => 'CV Agro Niaga Pantura Gemilang',
            'jenis_usaha' => 'distributor_besar',
            'kapasitas_serap_mingguan_kg' => 5000,
            'preferensi_grade' => 'semua',
            'alamat_gudang' => 'Kompleks Pergudangan Plered Blok B-3, Cirebon',
            'latitude' => -6.7120,
            'longitude' => 108.5123,
            'deskripsi_kebutuhan' => 'Distributor grosir melayani pasokan harian ke 4 pasar induk Jabodetabek & Jawa Barat.',
        ]);

        // UMKM: Bu Broto (Sambal & Saus Kemasan)
        $umkm1 = User::create([
            'name' => 'Ibu Sri Wahyuni (Bu Broto)',
            'email' => 'umkm@panenpulih.id',
            'password' => $password,
            'role' => 'umkm',
            'phone' => '081988899900',
            'address' => 'Jl. Mayor Dasuki No. 88, Jatibarang, Kab. Indramayu',
            'institution' => 'Dapur Olahan Sambal Bu Broto',
            'latitude' => -6.4715,
            'longitude' => 108.3142,
            'is_verified' => true,
        ]);

        PembeliProfile::create([
            'user_id' => $umkm1->id,
            'nama_usaha' => 'Dapur Olahan Sambal Bu Broto',
            'jenis_usaha' => 'umkm_olahan',
            'kapasitas_serap_mingguan_kg' => 800,
            'preferensi_grade' => 'imperfect',
            'alamat_gudang' => 'Sentra UMKM Jatibarang No. 12, Indramayu',
            'latitude' => -6.4715,
            'longitude' => 108.3142,
            'deskripsi_kebutuhan' => 'Produksi sambal botol dan bumbu pasta. Sangat siap menyerap cabai dan tomat imperfect produce (agak bengkok/ukuran kecil) asalkan segar.',
        ]);

        // 3. Kebutuhan Pembeli (Demands)
        $kebutuhanCabaiUMKM = KebutuhanPembeli::create([
            'kode_kebutuhan' => 'KBT-2026-001',
            'pembeli_id' => $umkm1->id,
            'komoditas_id' => $komoditasMap['Cabai Merah Keriting']->id,
            'jumlah_kg' => 300,
            'jumlah_terpenuhi_kg' => 300,
            'tanggal_mulai_butuh' => Carbon::now()->addDays(3)->toDateString(),
            'batas_tanggal_butuh' => Carbon::now()->addDays(10)->toDateString(),
            'grade_diterima' => 'imperfect',
            'budget_per_kg' => 30000,
            'lokasi' => 'Jatibarang, Indramayu',
            'latitude' => -6.4715,
            'longitude' => 108.3142,
            'tujuan_penggunaan' => 'olahan_makanan',
            'spesifikasi' => 'Cabai merah segar untuk digiling menjadi pasta sambal. Kulit merah matang, ukuran tidak seragam tidak masalah.',
            'status' => 'terpenuhi',
        ]);

        $kebutuhanTomatPedagang = KebutuhanPembeli::create([
            'kode_kebutuhan' => 'KBT-2026-002',
            'pembeli_id' => $pedagang1->id,
            'komoditas_id' => $komoditasMap['Tomat Sayur / Cerutu']->id,
            'jumlah_kg' => 600,
            'jumlah_terpenuhi_kg' => 0,
            'tanggal_mulai_butuh' => Carbon::now()->addDays(2)->toDateString(),
            'batas_tanggal_butuh' => Carbon::now()->addDays(8)->toDateString(),
            'grade_diterima' => 'semua',
            'budget_per_kg' => 10500,
            'lokasi' => 'Cirebon - Pantura',
            'latitude' => -6.7120,
            'longitude' => 108.5123,
            'tujuan_penggunaan' => 'pasar_tradisional',
            'spesifikasi' => 'Tomat segar semi-merah / mengkal siap kirim ke pasar induk, kemasan keranjang plastik.',
            'status' => 'aktif',
        ]);

        $kebutuhanCabaiPedagang = KebutuhanPembeli::create([
            'kode_kebutuhan' => 'KBT-2026-003',
            'pembeli_id' => $pedagang1->id,
            'komoditas_id' => $komoditasMap['Cabai Merah Keriting']->id,
            'jumlah_kg' => 500,
            'jumlah_terpenuhi_kg' => 0,
            'tanggal_mulai_butuh' => Carbon::now()->addDays(4)->toDateString(),
            'batas_tanggal_butuh' => Carbon::now()->addDays(12)->toDateString(),
            'grade_diterima' => 'grade_a',
            'budget_per_kg' => 33000,
            'lokasi' => 'Gudang Cirebon',
            'latitude' => -6.7120,
            'longitude' => 108.5123,
            'tujuan_penggunaan' => 'pasar_tradisional',
            'spesifikasi' => 'Cabai merah keriting kualitas super grade A untuk pasar swalayan dan agen pasar induk.',
            'status' => 'aktif',
        ]);

        $kebutuhanBawangPedagang = KebutuhanPembeli::create([
            'kode_kebutuhan' => 'KBT-2026-004',
            'pembeli_id' => $pedagang1->id,
            'komoditas_id' => $komoditasMap['Bawang Merah Brebes']->id,
            'jumlah_kg' => 400,
            'jumlah_terpenuhi_kg' => 0,
            'tanggal_mulai_butuh' => Carbon::now()->addDays(5)->toDateString(),
            'batas_tanggal_butuh' => Carbon::now()->addDays(15)->toDateString(),
            'grade_diterima' => 'semua',
            'budget_per_kg' => 35000,
            'lokasi' => 'Cirebon',
            'latitude' => -6.7120,
            'longitude' => 108.5123,
            'tujuan_penggunaan' => 'pasar_tradisional',
            'spesifikasi' => 'Bawang merah rogol atau ikatan kering, umbi padat.',
            'status' => 'aktif',
        ]);

        // 4. Rencana Panen Petani (Skenario Utama Dokumen Konsep Bab 1.3)
        // Kasus: Petani H = 1000 kg, P = 600 kg, K = 50 kg -> S = 350 kg Cabai Merah
        $rencanaPanen1 = RencanaPanen::create([
            'kode_rencana' => 'RPN-2026-001',
            'petani_id' => $petani1->id,
            'komoditas_id' => $komoditasMap['Cabai Merah Keriting']->id,
            'luas_lahan_m2' => 2500,
            'estimasi_hasil_kg' => 1000, // H = 1000 kg
            'jumlah_terpesan_kg' => 600,  // P = 600 kg
            'jumlah_alokasi_lain_kg' => 50, // K = 50 kg
            'potensi_surplus_kg' => 350,  // S = 350 kg
            'tanggal_tanam' => Carbon::now()->subDays(80)->toDateString(),
            'tanggal_perkiraan_panen' => Carbon::now()->addDays(5)->toDateString(), // 5 hari lagi
            'lokasi' => 'Blok Gandu, Desa Cantigi Kulon, Indramayu',
            'latitude' => -6.3264,
            'longitude' => 108.3200,
            'kondisi_tanaman' => 'sangat_baik',
            'grade' => 'campur',
            'fasilitas_penyimpanan' => 'shaded_shed',
            'ekspektasi_harga_per_kg' => 31000,
            'catatan' => 'Petikan perdana panen raya musim ini. Sekitar 600 kg sudah diikat kontrak tengkulak lokal, 50 kg untuk konsumsi kerabat.',
            'status' => 'aktif',
        ]);

        // Rencana Panen 2: Tomat Pak Supardi
        $rencanaPanen2 = RencanaPanen::create([
            'kode_rencana' => 'RPN-2026-002',
            'petani_id' => $petani1->id,
            'komoditas_id' => $komoditasMap['Tomat Sayur / Cerutu']->id,
            'luas_lahan_m2' => 3000,
            'estimasi_hasil_kg' => 2000, // H = 2000 kg
            'jumlah_terpesan_kg' => 1200, // P = 1200 kg
            'jumlah_alokasi_lain_kg' => 100, // K = 100 kg
            'potensi_surplus_kg' => 700,  // S = 700 kg
            'tanggal_tanam' => Carbon::now()->subDays(65)->toDateString(),
            'tanggal_perkiraan_panen' => Carbon::now()->addDays(3)->toDateString(), // 3 hari lagi (Mendesak!)
            'lokasi' => 'Cantigi Kulon, Indramayu',
            'latitude' => -6.3264,
            'longitude' => 108.3200,
            'kondisi_tanaman' => 'baik',
            'grade' => 'grade_b',
            'fasilitas_penyimpanan' => 'open_air',
            'ekspektasi_harga_per_kg' => 10000,
            'catatan' => 'Cuaca panas memicu pematangan serentak. Butuh penyerapan cepat sebelum buah membusuk di lahan.',
            'status' => 'aktif',
        ]);

        // Rencana Panen 3: Bawang Merah Bu Siti (Ciwidey)
        $rencanaPanen3 = RencanaPanen::create([
            'kode_rencana' => 'RPN-2026-003',
            'petani_id' => $petani2->id,
            'komoditas_id' => $komoditasMap['Bawang Merah Brebes']->id,
            'luas_lahan_m2' => 2000,
            'estimasi_hasil_kg' => 1500,
            'jumlah_terpesan_kg' => 1100,
            'jumlah_alokasi_lain_kg' => 100,
            'potensi_surplus_kg' => 300,
            'tanggal_tanam' => Carbon::now()->subDays(60)->toDateString(),
            'tanggal_perkiraan_panen' => Carbon::now()->addDays(12)->toDateString(),
            'lokasi' => 'Pasirjambu, Ciwidey, Kab. Bandung',
            'latitude' => -7.0945,
            'longitude' => 107.5020,
            'kondisi_tanaman' => 'sangat_baik',
            'grade' => 'grade_a',
            'fasilitas_penyimpanan' => 'ventilated_warehouse',
            'ekspektasi_harga_per_kg' => 36000,
            'catatan' => 'Umbi ukuran sedang-besar, kondisi kering gantung.',
            'status' => 'aktif',
        ]);

        // 5. Jalankan Service Intelligence & Matching untuk seluruh Rencana Panen
        $intelligenceService = new SurplusIntelligenceService();
        $matchingService = new BuyerMatchingService();

        foreach ([$rencanaPanen1, $rencanaPanen2, $rencanaPanen3] as $rencana) {
            $intelligenceService->analisa($rencana);
            $matchingService->cocokanUntukRencanaPanen($rencana);
        }

        // 6. Transaksi Penawaran & Penyaluran Realisasi (Simulasi Penyerapan Sukses Bab 1.3 & 11)
        // Bu Broto memesan 300 kg cabai merah surplus dari Rencana Panen 1
        $penawaran1 = Penawaran::create([
            'kode_penawaran' => 'PNW-2026-001',
            'rencana_panen_id' => $rencanaPanen1->id,
            'kebutuhan_pembeli_id' => $kebutuhanCabaiUMKM->id,
            'petani_id' => $petani1->id,
            'pembeli_id' => $umkm1->id,
            'inisiator_role' => 'umkm',
            'jumlah_kg' => 300,
            'harga_per_kg' => 30000,
            'total_harga' => 9000000,
            'tanggal_pengambilan' => Carbon::now()->addDays(5)->toDateString(),
            'metode_pengiriman' => 'diambil_pembeli',
            'catatan' => 'Bu Broto mengambil langsung menggunakan mobil bak tertutup ke lokasi panen Cantigi.',
            'status' => 'selesai',
        ]);

        // Realisasi Penyaluran (Penyaluran dan perhitungan tingkat penyerapan 85.71%)
        // Dari potensi surplus 350 kg, tersalurkan 300 kg -> tingkat penyerapan = 300 / 350 = 85.71%
        $penyaluran1 = Penyaluran::create([
            'kode_penyaluran' => 'DSTR-2026-001',
            'penawaran_id' => $penawaran1->id,
            'tanggal_penyaluran' => Carbon::now()->toDateString(),
            'jumlah_rencana_kg' => 300,
            'jumlah_realisasi_kg' => 300,
            'sisa_surplus_kg' => 50,
            'tingkat_penyerapan_persen' => 85.71, // (300 / 350) * 100%
            'harga_total_realisasi' => 9000000,
            'status_kualitas' => 'sesuai_ekspektasi',
            'bukti_penyaluran' => 'surat_jalan_dstr001.pdf',
            'catatan_penyaluran' => 'Hasil panen 300 kg cabai merah keriting diterima dalam kondisi segar, langsung diproses ke mesin pencucian & penggilingan sambal.',
            'status_verifikasi' => 'terverifikasi',
            'rating_pembeli' => 5,
            'ulasan_pembeli' => 'Cabai sangat segar dan pedas mantap! Pengolahan sambal kami tertolong dengan harga kompetitif dan petani terhindar dari rugi panen.',
            'dampak_food_loss_terhindar_kg' => 300,
            'dampak_nilai_ekonomi_rp' => 9000000,
        ]);

        // Penawaran Aktif (Sedang Berjalan): Penawaran dari H. Hendra untuk Tomat 500 kg
        $penawaran2 = Penawaran::create([
            'kode_penawaran' => 'PNW-2026-002',
            'rencana_panen_id' => $rencanaPanen2->id,
            'kebutuhan_pembeli_id' => $kebutuhanTomatPedagang->id,
            'petani_id' => $petani1->id,
            'pembeli_id' => $pedagang1->id,
            'inisiator_role' => 'pedagang',
            'jumlah_kg' => 500,
            'harga_per_kg' => 10000,
            'total_harga' => 5000000,
            'tanggal_pengambilan' => Carbon::now()->addDays(3)->toDateString(),
            'metode_pengiriman' => 'diambil_pembeli',
            'catatan' => 'Truk angkut siap jemput di tepi jalan raya Cantigi. Harap dipetik sore hari.',
            'status' => 'menunggu_konfirmasi',
        ]);

        // 7. Catat Audit Trail Riwayat Status
        RiwayatStatus::catat('rencana_panen', $rencanaPanen1->id, null, 'aktif', $petani1->id, 'Rencana panen cabai didaftarkan.');
        RiwayatStatus::catat('penawaran', $penawaran1->id, 'menunggu_konfirmasi', 'disetujui', $petani1->id, 'Petani menyetujui penawaran Bu Broto.');
        RiwayatStatus::catat('penyaluran', $penyaluran1->id, 'menunggu_verifikasi', 'terverifikasi', $umkm1->id, 'Bu Broto mengonfirmasi penerimaan 300 kg cabai di gudang.');
    }
}
