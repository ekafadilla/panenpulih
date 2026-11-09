<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetaniProfile extends Model
{
    use HasFactory;

    protected $table = 'petani';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'nama_poktan',
        'luas_lahan_ha',
        'alamat_lahan',
        'fasilitas_penyimpanan',
        'latitude',
        'longitude',
        'kontak_darurat',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'luas_lahan_ha' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
