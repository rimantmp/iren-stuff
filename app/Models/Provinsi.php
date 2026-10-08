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
    ];

    /**
     * @return HasMany<Kota, $this>
     */
    public function kota()
    {
        return $this->hasMany(Kota::class, 'id', 'id');
    }
}
