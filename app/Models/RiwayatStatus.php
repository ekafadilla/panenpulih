<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatus extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'referensi_tipe',
        'referensi_id',
        'status_lama',
        'status_baru',
        'user_id',
        'catatan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper to log status transition
     */
    public static function catat(string $tipe, int $id, ?string $lama, string $baru, ?int $userId = null, ?string $catatan = null): self
    {
        return self::create([
            'referensi_tipe' => $tipe,
            'referensi_id' => $id,
            'status_lama' => $lama,
            'status_baru' => $baru,
            'user_id' => $userId ?? auth()->id(),
            'catatan' => $catatan,
        ]);
    }
}
