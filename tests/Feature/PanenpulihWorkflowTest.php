<?php

namespace Tests\Feature;

use App\Models\KebutuhanPembeli;
use App\Models\Komoditas;
use App\Models\Penawaran;
use App\Models\Penyaluran;
use App\Models\RencanaPanen;
use App\Models\User;
use Database\Seeders\PanenpulihDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanenpulihWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PanenpulihDatabaseSeeder::class);
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('PANENPULIH');
        $response->assertSee('PRE-HARVEST SURPLUS INTELLIGENCE');
    }

    public function test_surplus_simulator_api_calculates_correctly(): void
    {
        // Kasus Bab 1.3: H = 1000 kg, P = 600 kg, K = 50 kg -> S = 350 kg
        $response = $this->postJson('/api/simulasi', [
            'estimasi_hasil_kg' => 1000,
            'jumlah_terpesan_kg' => 600,
            'jumlah_alokasi_lain_kg' => 50,
            'hari_menuju_panen' => 5,
            'skor_kerentanan' => 85,
            'fasilitas_penyimpanan' => 'shaded_shed',
            'permintaan_pasar_kg' => 400,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'potensi_surplus_kg' => 350,
            'persentase_surplus' => 35,
        ]);

        $this->assertGreaterThan(50, $response->json('skor_risiko_total'));
    }

    public function test_petani_can_create_harvest_plan_and_triggers_intelligence(): void
    {
        $petani = User::where('role', 'petani')->first();
        $komoditas = Komoditas::first();

        $this->actingAs($petani);

        $response = $this->post(route('petani.rencana-panen.store'), [
            'komoditas_id' => $komoditas->id,
            'luas_lahan_m2' => 2000,
            'estimasi_hasil_kg' => 1200,
            'jumlah_terpesan_kg' => 700,
            'jumlah_alokasi_lain_kg' => 100,
            'tanggal_perkiraan_panen' => now()->addDays(7)->toDateString(),
            'lokasi' => 'Cantigi, Indramayu',
            'kondisi_tanaman' => 'sangat_baik',
            'grade' => 'grade_a',
            'fasilitas_penyimpanan' => 'shaded_shed',
            'ekspektasi_harga_per_kg' => 32000,
        ]);

        $rencana = RencanaPanen::where('petani_id', $petani->id)->latest('id')->first();

        $this->assertNotNull($rencana);
        // S = 1200 - 700 - 100 = 400 kg
        $this->assertEquals(400, (int) $rencana->potensi_surplus_kg);
        $this->assertNotNull($rencana->analisisSurplus);
        $this->assertEquals(400, (int) $rencana->analisisSurplus->potensi_surplus_kg);

        $response->assertRedirect(route('petani.rencana-panen.show', $rencana->id));
    }

    public function test_buyer_can_browse_and_submit_offer(): void
    {
        $pembeli = User::where('role', 'umkm')->first();
        $rencana = RencanaPanen::where('potensi_surplus_kg', '>', 0)->first();

        $this->actingAs($pembeli);

        // Akses detail surplus
        $response = $this->get(route('pembeli.jelajah-surplus.detail', $rencana->id));
        $response->assertStatus(200);

        // Submit penawaran
        $response = $this->post(route('pembeli.penawaran.buat'), [
            'rencana_panen_id' => $rencana->id,
            'jumlah_kg' => 150,
            'harga_per_kg' => 30000,
            'tanggal_pengambilan' => now()->addDays(5)->toDateString(),
            'metode_pengiriman' => 'diambil_pembeli',
            'catatan' => 'Uji coba penawaran otomatis',
        ]);

        $response->assertRedirect(route('pembeli.penawaran.index'));

        $penawaran = Penawaran::where('pembeli_id', $pembeli->id)
            ->where('rencana_panen_id', $rencana->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($penawaran);
        $this->assertEquals(150, (int) $penawaran->jumlah_kg);
        $this->assertEquals(4500000, (int) $penawaran->total_harga);
        $this->assertEquals('menunggu_konfirmasi', $penawaran->status);
    }

    public function test_farmer_can_accept_offer_and_record_realization_with_absorption_rate(): void
    {
        $penawaran = Penawaran::where('status', 'menunggu_konfirmasi')->first();
        $petani = $penawaran->petani;

        $this->actingAs($petani);

        // Terima penawaran
        $response = $this->post(route('petani.penawaran.terima', $penawaran->id));
        $response->assertStatus(302);

        $penawaran->refresh();
        $this->assertEquals('disetujui', $penawaran->status);

        // Catat penyaluran realisasi (Novelty 3 & Section 11)
        $response = $this->post(route('petani.penyaluran.simpanCatat', $penawaran->id), [
            'tanggal_penyaluran' => now()->toDateString(),
            'jumlah_realisasi_kg' => $penawaran->jumlah_kg,
            'harga_total_realisasi' => $penawaran->total_harga,
            'hasil_aktual_kg' => $penawaran->rencanaPanen->estimasi_hasil_kg,
            'status_kualitas' => 'sesuai_ekspektasi',
            'catatan_penyaluran' => 'Penyerapan sukses via test',
        ]);

        $response->assertRedirect(route('petani.penyaluran.index'));

        $penyaluran = Penyaluran::where('penawaran_id', $penawaran->id)->first();
        $this->assertNotNull($penyaluran);
        $this->assertGreaterThan(0, $penyaluran->tingkat_penyerapan_persen);
        $this->assertEquals('terverifikasi', $penyaluran->status_verifikasi);
    }

    public function test_buyer_can_confirm_receipt_and_give_rating(): void
    {
        $penyaluran = Penyaluran::where('status_verifikasi', 'terverifikasi')->first();
        $pembeli = $penyaluran->penawaran->pembeli;

        $this->actingAs($pembeli);

        $response = $this->post(route('pembeli.penyaluran.konfirmasi', $penyaluran->id), [
            'rating_pembeli' => 5,
            'ulasan_pembeli' => 'Hasil panen sangat segar dan menguntungkan kedua belah pihak.',
        ]);

        $response->assertStatus(302);

        $penyaluran->refresh();
        $this->assertEquals('selesai', $penyaluran->status_verifikasi);
        $this->assertEquals(5, $penyaluran->rating_pembeli);
    }

    public function test_admin_can_access_dashboard_and_sdg_report(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('National Pre-Harvest Surplus', false);

        $response = $this->get(route('admin.laporan.sdg'));
        $response->assertStatus(200);
        $response->assertSee('SDG 12.3: Food Loss Dicegah');
        $response->assertSee('SDG 2: Nol Kelaparan');
    }

    public function test_fast_demo_switcher_switches_session(): void
    {
        $response = $this->get(route('auth.demo', 'admin'));
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);

        $response = $this->get(route('auth.demo', 'umkm'));
        $response->assertRedirect(route('pembeli.dashboard'));
        $this->assertEquals('umkm', auth()->user()->role);
    }
}
