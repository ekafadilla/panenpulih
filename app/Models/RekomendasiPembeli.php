<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekomendasiPembeli extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi_pembeli';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'rencana_panen_id',
        'kebutuhan_pembeli_id',
        'skor_total',
        'skor_komoditas',
        'skor_kuantitas',
        'skor_lokasi',
        'skor_waktu',
        'skor_kualitas',
        'jarak_km',
        'alasan_rekomendasi',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'skor_total' => 'float',
            'skor_komoditas' => 'float',
            'skor_kuantitas' => 'float',
            'skor_lokasi' => 'float',
            'skor_waktu' => 'float',
            'skor_kualitas' => 'float',
            'jarak_km' => 'float',
        ];
    }

    public function rencanaPanen(): BelongsTo
    {
        return $this->belongsTo(RencanaPanen::class, 'rencana_panen_id');
    }

    public function kebutuhanPembeli(): BelongsTo
    {
        return $this->belongsTo(KebutuhanPembeli::class, 'kebutuhan_pembeli_id');
    }
}
