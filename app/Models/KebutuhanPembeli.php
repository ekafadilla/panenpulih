<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KebutuhanPembeli extends Model
{
    use HasFactory;

    protected $table = 'kebutuhan_pembeli';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'kode_kebutuhan',
        'pembeli_id',
        'komoditas_id',
        'jumlah_kg',
        'jumlah_terpenuhi_kg',
        'tanggal_mulai_butuh',
        'batas_tanggal_butuh',
        'grade_diterima',
        'budget_per_kg',
        'lokasi',
        'latitude',
        'longitude',
        'tujuan_penggunaan',
        'spesifikasi',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_kg' => 'float',
            'jumlah_terpenuhi_kg' => 'float',
            'budget_per_kg' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
            'tanggal_mulai_butuh' => 'date',
            'batas_tanggal_butuh' => 'date',
        ];
    }

    public function pembeli(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembeli_id');
    }

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }

    public function rekomendasi(): HasMany
    {
        return $this->hasMany(RekomendasiPembeli::class, 'kebutuhan_pembeli_id');
    }

    public function penawaran(): HasMany
    {
        return $this->hasMany(Penawaran::class, 'kebutuhan_pembeli_id');
    }
}
