<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBantuan extends Model
{
    protected $table = 'jenis_bantuan';

    protected $fillable = [
        'nama',
        'slug',
        'satuan',
        'deskripsi',
        'icon',
        'status_aktif',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    /**
     * @return HasMany<PenyaluranBantuan, $this>
     */
    public function penyaluran(): HasMany
    {
        return $this->hasMany(PenyaluranBantuan::class, 'jenis_bantuan_id');
    }
}
