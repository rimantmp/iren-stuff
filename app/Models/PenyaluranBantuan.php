<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyaluranBantuan extends Model
{
    protected $table = 'penyaluran_bantuan';

    protected $fillable = [
        'kode_transaksi',
        'jenis_bantuan_id',
        'provinsi_id',
        'kota_id',
        'kecamatan_id',
        'kelurahan_id',
        'dusun_id',
        'alamat_detail',
        'latitude',
        'longitude',
        'nama_penerima',
        'kontak_penerima',
        'jumlah_kk',
        'jumlah_jiwa',
        'jumlah_bantuan',
        'satuan',
        'tanggal_rencana',
        'tanggal_penyaluran',
        'status',
        'metode_distribusi',
        'nomor_armada',
        'nama_petugas',
        'sumber_air',
        'foto_dokumentasi',
        'catatan',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_rencana' => 'date',
        'tanggal_penyaluran' => 'date',
        'jumlah_bantuan' => 'double',
        'jumlah_kk' => 'integer',
        'jumlah_jiwa' => 'integer',
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    /**
     * @return BelongsTo<JenisBantuan, $this>
     */
    public function jenisBantuan(): BelongsTo
    {
        return $this->belongsTo(JenisBantuan::class, 'jenis_bantuan_id');
    }

    /**
     * @return BelongsTo<Provinsi, $this>
     */
    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }

    /**
     * @return BelongsTo<Kota, $this>
     */
    public function kota(): BelongsTo
    {
        return $this->belongsTo(Kota::class, 'kota_id', 'id');
    }

    /**
     * @return BelongsTo<Kecamatan, $this>
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }

    /**
     * @return BelongsTo<Kelurahan, $this>
     */
    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id', 'id');
    }

    /**
     * @return BelongsTo<Dusun, $this>
     */
    public function dusun(): BelongsTo
    {
        return $this->belongsTo(Dusun::class, 'dusun_id', 'id');
    }
}
