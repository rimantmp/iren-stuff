<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    protected $table = 't_provinsi';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nama',
        'latitude',
        'longitude',
        'status_aktif',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    /**
     * Scope a query to only include active provinsi.
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * @return HasMany<Kota, $this>
     */
    public function kota()
    {
        return $this->hasMany(Kota::class, 'id', 'id');
    }
}
