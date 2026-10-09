<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    protected $table = 't_kota';

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
     * Scope a query to only include active kota.
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }
}
