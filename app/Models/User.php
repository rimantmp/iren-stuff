<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'permissions'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PETUGAS_WILAYAH = 'petugas_wilayah';

    public const ROLE_CUSTOM = 'custom';

    public const ROLES = [
        self::ROLE_ADMIN => 'Administrator (Akses Penuh)',
        self::ROLE_PETUGAS_WILAYAH => 'Petugas Wilayah (Khusus Master Wilayah)',
        self::ROLE_CUSTOM => 'Kustom (Pilih Hak Akses Mandiri)',
    ];

    public const PERMISSIONS = [
        'wilayah' => [
            'label' => 'Master Data Wilayah',
            'desc' => 'Kelola Provinsi, Kota/Kabupaten, Kecamatan, Kelurahan/Desa, dan Dusun',
        ],
        'bantuan_air' => [
            'label' => 'Penyaluran Bantuan Air',
            'desc' => 'Input, edit, cetak, dan monitoring bantuan air bersih',
        ],
        'rekap' => [
            'label' => 'Rekapitulasi & Laporan',
            'desc' => 'Akses laporan rekap, perbandingan wilayah, dan ekspor Excel',
        ],
        'users' => [
            'label' => 'Manajemen Pengguna',
            'desc' => 'Tambah, edit, dan kelola akun pengguna serta hak akses',
        ],
        'backup' => [
            'label' => 'Backup Database',
            'desc' => 'Generate dan unduh cadangan basis data sistem',
        ],
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    /**
     * Cek apakah pengguna berstatus Administrator penuh.
     */
    public function isAdmin(): bool
    {
        return empty($this->role) || $this->role === self::ROLE_ADMIN;
    }

    /**
     * Cek apakah pengguna adalah Petugas Wilayah.
     */
    public function isPetugasWilayah(): bool
    {
        return $this->role === self::ROLE_PETUGAS_WILAYAH;
    }

    /**
     * Cek apakah pengguna memiliki salah satu role yang ditentukan.
     *
     * @param  string|array<int, string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    /**
     * Cek apakah pengguna memiliki hak akses (permission) untuk modul tertentu.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isPetugasWilayah()) {
            return $permission === 'wilayah';
        }

        $userPermissions = $this->permissions ?? [];

        return in_array($permission, $userPermissions, true);
    }

    /**
     * Cek apakah pengguna memiliki setidaknya salah satu izin dari daftar.
     *
     * @param  array<int, string>  $permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dapatkan label human-readable untuk role pengguna.
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst(str_replace('_', ' ', (string) $this->role));
    }
}
