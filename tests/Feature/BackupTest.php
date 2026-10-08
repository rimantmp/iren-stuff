<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Gunakan fake storage disk untuk mengisolasi pengujian file
        Storage::fake('local');
    }

    public function test_guests_cannot_access_backup_features(): void
    {
        $responseIndex = $this->get(route('admin.backup.index'));
        $responseIndex->assertRedirect(route('login'));

        $responseStore = $this->post(route('admin.backup.store'));
        $responseStore->assertRedirect(route('login'));

        $responseDownload = $this->get(route('admin.backup.download', ['filename' => 'backup_test.sql']));
        $responseDownload->assertRedirect(route('login'));

        $responseDestroy = $this->delete(route('admin.backup.destroy', ['filename' => 'backup_test.sql']));
        $responseDestroy->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_backup_index(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.backup.index'));

        $response->assertStatus(200);
        $response->assertSee('Backup Basis Data');
        $response->assertSee('Buat Cadangan Baru');
        $response->assertSee('Riwayat File Cadangan');
    }

    public function test_authenticated_user_can_generate_backup(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.backup.store'));

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('success');

        $files = Storage::disk('local')->files('backups');
        $this->assertNotEmpty($files);

        $backupFile = $files[0];
        $this->assertStringEndsWith('.sql', $backupFile);

        $content = Storage::disk('local')->get($backupFile);
        $this->assertStringContainsString('File Cadangan Basis Data', $content);
        $this->assertStringContainsString('users', $content);
    }

    public function test_authenticated_user_can_download_existing_backup(): void
    {
        $admin = User::factory()->create();

        // Buat file backup palsu
        $filename = 'backup_test_2026.sql';
        Storage::disk('local')->put("backups/{$filename}", '-- SQL TEST DUMP DATA');

        $response = $this->actingAs($admin)->get(route('admin.backup.download', ['filename' => $filename]));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename="'.$filename.'"');
    }

    public function test_download_fails_for_non_existent_file(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.backup.download', ['filename' => 'tidak_ada.sql']));

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('error');
    }

    public function test_path_traversal_is_prevented_on_download(): void
    {
        $admin = User::factory()->create();

        // Coba injeksi path traversal
        $response = $this->actingAs($admin)->get(route('admin.backup.download', ['filename' => '../../etc/passwd.sql']));

        // Harus dialihkan kembali dengan pesan error karena basename('passwd.sql') tidak ada di backups
        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('error');
    }

    public function test_authenticated_user_can_delete_backup(): void
    {
        $admin = User::factory()->create();

        $filename = 'backup_to_delete.sql';
        Storage::disk('local')->put("backups/{$filename}", '-- Content');

        $this->assertTrue(Storage::disk('local')->exists("backups/{$filename}"));

        $response = $this->actingAs($admin)->delete(route('admin.backup.destroy', ['filename' => $filename]));

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('success');
        $this->assertFalse(Storage::disk('local')->exists("backups/{$filename}"));
    }

    public function test_artisan_db_backup_command_runs_successfully(): void
    {
        $this->artisan('db:backup')
            ->expectsOutputToContain('Pencadangan berhasil!')
            ->assertSuccessful();

        $files = Storage::disk('local')->files('backups');
        $this->assertNotEmpty($files);
    }
}
