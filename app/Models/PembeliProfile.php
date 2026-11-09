<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembeliProfile extends Model
{
    use HasFactory;

    protected $table = 'pembeli';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'nama_usaha',
        'jenis_usaha',
        'kapasitas_serap_mingguan_kg',
        'preferensi_grade',
        'alamat_gudang',
        'latitude',
        'longitude',
        'deskripsi_kebutuhan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kapasitas_serap_mingguan_kg' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
