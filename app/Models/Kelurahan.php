<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelurahan extends Model
{
    protected $table = 't_kelurahan';

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
     * @return HasMany<Dusun, $this>
     */
    public function dusuns(): HasMany
    {
        return $this->hasMany(Dusun::class, 'kelurahan_id', 'id');
    }
}
