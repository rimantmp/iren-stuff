<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dusun extends Model
{
    use HasFactory;

    protected $table = 't_dusun';

    protected $fillable = [
        'kelurahan_id',
        'nama',
        'rw',
        'rt',
        'kepala_dusun',
        'latitude',
        'longitude',
        'keterangan',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    /**
     * @return BelongsTo<Kelurahan, $this>
     */
    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id', 'id');
    }
}
