<?php

namespace Tests\Feature;

use App\Models\JenisBantuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        JenisBantuan::create([
            'nama' => 'Bantuan Air Bersih',
            'slug' => 'air',
            'satuan' => 'Liter',
            'deskripsi' => 'Bantuan air bersih tangki',
        ]);
    }

    public function test_admin_user_has_access_to_all_modules(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('bantuan.air.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('master.provinsi'))->assertStatus(200);
        $this->actingAs($admin)->get(route('master.dusun'))->assertStatus(200);
        $this->actingAs($admin)->get(route('rekap.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.users.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.backup.index'))->assertStatus(200);
    }

    public function test_petugas_wilayah_can_access_master_wilayah_modules(): void
    {
        $petugas = User::factory()->create([
            'name' => 'Petugas Khusus Wilayah',
            'role' => User::ROLE_PETUGAS_WILAYAH,
        ]);

        // Dashboard dapat diakses dan menampilkan portal master wilayah
        $resDashboard = $this->actingAs($petugas)->get(route('admin.dashboard'));
        $resDashboard->assertStatus(200);
        $resDashboard->assertSee('Portal Master Wilayah');

        // Modul-modul master wilayah dapat diakses dengan sukses
        $this->actingAs($petugas)->get(route('master.provinsi'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('master.kota'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('master.kecamatan'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('master.kelurahan'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('master.dusun'))->assertStatus(200);
    }

    public function test_petugas_wilayah_is_forbidden_from_other_modules(): void
    {
        $petugas = User::factory()->create([
            'role' => User::ROLE_PETUGAS_WILAYAH,
        ]);

        // Harus mendapatkan 403 Forbidden di modul lain
        $this->actingAs($petugas)->get(route('bantuan.air.index'))->assertStatus(403);
        $this->actingAs($petugas)->get(route('bantuan.air.create'))->assertStatus(403);
        $this->actingAs($petugas)->get(route('rekap.index'))->assertStatus(403);
        $this->actingAs($petugas)->get(route('rekap.perbandingan'))->assertStatus(403);
        $this->actingAs($petugas)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($petugas)->get(route('admin.backup.index'))->assertStatus(403);
    }

    public function test_custom_user_with_specific_permissions(): void
    {
        $userBantuanOnly = User::factory()->create([
            'role' => User::ROLE_CUSTOM,
            'permissions' => ['bantuan_air'],
        ]);

        // Dapat mengakses bantuan air
        $this->actingAs($userBantuanOnly)->get(route('bantuan.air.index'))->assertStatus(200);

        // Dilarang mengakses master wilayah dan manajemen user
        $this->actingAs($userBantuanOnly)->get(route('master.dusun'))->assertStatus(403);
        $this->actingAs($userBantuanOnly)->get(route('admin.users.index'))->assertStatus(403);
    }

    public function test_admin_can_create_user_with_petugas_wilayah_role(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Wilayah Baru',
            'email' => 'wilayah@bantuan.id',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => User::ROLE_PETUGAS_WILAYAH,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Petugas Wilayah Baru',
            'email' => 'wilayah@bantuan.id',
            'role' => User::ROLE_PETUGAS_WILAYAH,
        ]);

        $created = User::where('email', 'wilayah@bantuan.id')->first();
        $this->assertTrue($created->isPetugasWilayah());
        $this->assertTrue($created->hasPermission('wilayah'));
        $this->assertFalse($created->hasPermission('bantuan_air'));
    }

    public function test_admin_cannot_demote_own_account_from_admin(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $admin->id), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => User::ROLE_PETUGAS_WILAYAH,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(User::ROLE_ADMIN, $admin->fresh()->role);
    }

    public function test_admin_can_filter_user_list_by_role(): void
    {
        $admin = User::factory()->create([
            'name' => 'Current Logged Admin',
            'role' => User::ROLE_ADMIN,
        ]);

        $adminTarget = User::factory()->create([
            'name' => 'User Target Admin',
            'role' => User::ROLE_ADMIN,
        ]);

        $petugasTarget = User::factory()->create([
            'name' => 'User Target Petugas',
            'role' => User::ROLE_PETUGAS_WILAYAH,
        ]);

        $resAdminFilter = $this->actingAs($admin)->get(route('admin.users.index', ['role' => User::ROLE_ADMIN]));
        $resAdminFilter->assertStatus(200);
        $resAdminFilter->assertSee('User Target Admin');
        $resAdminFilter->assertDontSee('User Target Petugas');

        $resWilayahFilter = $this->actingAs($admin)->get(route('admin.users.index', ['role' => User::ROLE_PETUGAS_WILAYAH]));
        $resWilayahFilter->assertStatus(200);
        $resWilayahFilter->assertSee('User Target Petugas');
        $resWilayahFilter->assertDontSee('User Target Admin');
    }
}
