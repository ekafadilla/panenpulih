<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penawaran extends Model
{
    use HasFactory;

    protected $table = 'penawaran';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'kode_penawaran',
        'rencana_panen_id',
        'kebutuhan_pembeli_id',
        'petani_id',
        'pembeli_id',
        'inisiator_role',
        'jumlah_kg',
        'harga_per_kg',
        'total_harga',
        'tanggal_pengambilan',
        'metode_pengiriman',
        'catatan',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_kg' => 'float',
            'harga_per_kg' => 'float',
            'total_harga' => 'float',
            'tanggal_pengambilan' => 'date',
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

    public function petani(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petani_id');
    }

    public function pembeli(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembeli_id');
    }

    public function penyaluran(): HasOne
    {
        return $this->hasOne(Penyaluran::class, 'penawaran_id');
    }
}
