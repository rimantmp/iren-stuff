<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_admin_list_and_create_page(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@bantuan.id',
        ]);

        $responseIndex = $this->actingAs($admin)->get(route('admin.users.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Admin Utama');
        $responseIndex->assertSee('admin@bantuan.id');
        $responseIndex->assertSee('Tambah Pengguna Baru');

        $responseCreate = $this->actingAs($admin)->get(route('admin.users.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Formulir Pendaftaran Pengguna');
    }

    public function test_can_create_new_administrator(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Lapangan',
            'email' => 'petugas@bantuan.id',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Petugas Lapangan',
            'email' => 'petugas@bantuan.id',
        ]);

        $newUser = User::where('email', 'petugas@bantuan.id')->first();
        $this->assertTrue(Hash::check('secret123', $newUser->password));
    }

    public function test_create_admin_validation_errors(): void
    {
        $admin = User::factory()->create([
            'email' => 'existing@bantuan.id',
        ]);

        // Duplicate email & mismatched password
        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Baru',
            'email' => 'existing@bantuan.id',
            'password' => 'secret123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_can_update_administrator_details(): void
    {
        $admin = User::factory()->create();
        $targetUser = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@bantuan.id',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $targetUser->id), [
            'name' => 'Updated Name',
            'email' => 'updated@bantuan.id',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@bantuan.id',
        ]);

        $this->assertTrue(Hash::check('newpassword123', $targetUser->fresh()->password));
    }

    public function test_cannot_delete_own_logged_in_account(): void
    {
        $admin = User::factory()->create();
        User::factory()->create(); // second user

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_can_delete_another_admin(): void
    {
        $admin = User::factory()->create();
        $otherAdmin = User::factory()->create([
            'name' => 'Admin To Delete',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $otherAdmin->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $otherAdmin->id]);
    }
}
