<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Komoditas extends Model
{
    use HasFactory;

    protected $table = 'komoditas';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'nama_latin',
        'kategori',
        'umur_simpan_hari',
        'skor_kerentanan',
        'satuan',
        'harga_acuan_per_kg',
        'deskripsi',
        'pedoman_penyimpanan',
        'gambar',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'umur_simpan_hari' => 'integer',
            'skor_kerentanan' => 'integer',
            'harga_acuan_per_kg' => 'float',
        ];
    }

    public function rencanaPanen(): HasMany
    {
        return $this->hasMany(RencanaPanen::class, 'komoditas_id');
    }

    public function kebutuhanPembeli(): HasMany
    {
        return $this->hasMany(KebutuhanPembeli::class, 'komoditas_id');
    }
}
