<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RencanaPanen extends Model
{
    use HasFactory;

    protected $table = 'rencana_panen';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'kode_rencana',
        'petani_id',
        'komoditas_id',
        'luas_lahan_m2',
        'estimasi_hasil_kg',
        'jumlah_terpesan_kg',
        'jumlah_alokasi_lain_kg',
        'potensi_surplus_kg',
        'tanggal_tanam',
        'tanggal_perkiraan_panen',
        'tanggal_panen_aktual',
        'hasil_aktual_kg',
        'lokasi',
        'latitude',
        'longitude',
        'kondisi_tanaman',
        'grade',
        'fasilitas_penyimpanan',
        'ekspektasi_harga_per_kg',
        'foto',
        'catatan',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'luas_lahan_m2' => 'float',
            'estimasi_hasil_kg' => 'float',
            'jumlah_terpesan_kg' => 'float',
            'jumlah_alokasi_lain_kg' => 'float',
            'potensi_surplus_kg' => 'float',
            'hasil_aktual_kg' => 'float',
            'ekspektasi_harga_per_kg' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
            'tanggal_tanam' => 'date',
            'tanggal_perkiraan_panen' => 'date',
            'tanggal_panen_aktual' => 'date',
        ];
    }

    public function petani(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petani_id');
    }

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }

    public function analisisSurplus(): HasOne
    {
        return $this->hasOne(AnalisisSurplus::class, 'rencana_panen_id');
    }

    public function rekomendasiPembeli(): HasMany
    {
        return $this->hasMany(RekomendasiPembeli::class, 'rencana_panen_id');
    }

    public function penawaran(): HasMany
    {
        return $this->hasMany(Penawaran::class, 'rencana_panen_id');
    }
}
