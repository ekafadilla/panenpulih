<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penyaluran extends Model
{
    use HasFactory;

    protected $table = 'penyaluran';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'kode_penyaluran',
        'penawaran_id',
        'tanggal_penyaluran',
        'jumlah_rencana_kg',
        'jumlah_realisasi_kg',
        'sisa_surplus_kg',
        'tingkat_penyerapan_persen',
        'harga_total_realisasi',
        'status_kualitas',
        'bukti_penyaluran',
        'catatan_penyaluran',
        'status_verifikasi',
        'rating_pembeli',
        'ulasan_pembeli',
        'dampak_food_loss_terhindar_kg',
        'dampak_nilai_ekonomi_rp',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_penyaluran' => 'date',
            'jumlah_rencana_kg' => 'float',
            'jumlah_realisasi_kg' => 'float',
            'sisa_surplus_kg' => 'float',
            'tingkat_penyerapan_persen' => 'float',
            'harga_total_realisasi' => 'float',
            'rating_pembeli' => 'integer',
            'dampak_food_loss_terhindar_kg' => 'float',
            'dampak_nilai_ekonomi_rp' => 'float',
        ];
    }

    public function penawaran(): BelongsTo
    {
        return $this->belongsTo(Penawaran::class, 'penawaran_id');
    }
}
