<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalisisSurplus extends Model
{
    use HasFactory;

    protected $table = 'analisis_surplus';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'rencana_panen_id',
        'potensi_surplus_kg',
        'persentase_surplus',
        'skor_persentase_surplus',
        'skor_waktu',
        'skor_kerentanan',
        'skor_penyimpanan',
        'skor_permintaan',
        'skor_risiko_total',
        'kategori_risiko',
        'hari_menuju_panen',
        'rekomendasi_tindakan',
        'catatan_analisis',
        'tanggal_analisis',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'potensi_surplus_kg' => 'float',
            'persentase_surplus' => 'float',
            'skor_persentase_surplus' => 'float',
            'skor_waktu' => 'float',
            'skor_kerentanan' => 'float',
            'skor_penyimpanan' => 'float',
            'skor_permintaan' => 'float',
            'skor_risiko_total' => 'float',
            'hari_menuju_panen' => 'integer',
            'tanggal_analisis' => 'date',
        ];
    }

    public function rencanaPanen(): BelongsTo
    {
        return $this->belongsTo(RencanaPanen::class, 'rencana_panen_id');
    }

    public function getRiskBadgeClassAttribute(): string
    {
        return match ($this->kategori_risiko) {
            'Rendah' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'Sedang' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'Tinggi' => 'bg-orange-100 text-orange-800 border-orange-300',
            'Sangat Tinggi' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
