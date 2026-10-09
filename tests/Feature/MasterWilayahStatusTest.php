<?php

namespace Tests\Feature;

use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\Provinsi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterWilayahStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Seed basic Provinsi
        Provinsi::create(['id' => '73', 'nama' => 'Sulawesi Selatan', 'status_aktif' => true]);
        Provinsi::create(['id' => '31', 'nama' => 'DKI Jakarta', 'status_aktif' => true]);

        // Seed basic Kota
        Kota::create(['id' => '7318', 'nama' => 'Tana Toraja', 'status_aktif' => true]);
        Kota::create(['id' => '7326', 'nama' => 'Toraja Utara', 'status_aktif' => true]);
        Kota::create(['id' => '7371', 'nama' => 'Makassar', 'status_aktif' => true]);
        Kota::create(['id' => '3171', 'nama' => 'Jakarta Selatan', 'status_aktif' => true]);
    }

    public function test_admin_can_view_provinsi_and_kota_pages_with_status_filter(): void
    {
        $responseProv = $this->actingAs($this->user)->get(route('master.provinsi', ['status' => 'aktif']));
        $responseProv->assertOk();
        $responseProv->assertSee('Sulawesi Selatan');

        $responseKota = $this->actingAs($this->user)->get(route('master.kota', ['status' => 'aktif']));
        $responseKota->assertOk();
        $responseKota->assertSee('Toraja Utara');
    }

    public function test_admin_can_toggle_provinsi_status(): void
    {
        $provinsi = Provinsi::find('31');
        $this->assertTrue($provinsi->status_aktif);

        $response = $this->actingAs($this->user)->patch(route('master.provinsi.toggle', '31'));
        $response->assertRedirect();

        $provinsi->refresh();
        $this->assertFalse($provinsi->status_aktif);

        // Toggle again to activate
        $response = $this->actingAs($this->user)->patch(route('master.provinsi.toggle', '31'));
        $response->assertRedirect();

        $provinsi->refresh();
        $this->assertTrue($provinsi->status_aktif);
    }

    public function test_admin_can_toggle_kota_status(): void
    {
        $kota = Kota::find('7371');
        $this->assertTrue($kota->status_aktif);

        $response = $this->actingAs($this->user)->patch(route('master.kota.toggle', '7371'));
        $response->assertRedirect();

        $kota->refresh();
        $this->assertFalse($kota->status_aktif);
    }

    public function test_admin_can_apply_preset_sulsel_for_provinsi(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.provinsi.batch-status'), [
            'action' => 'preset_sulsel',
        ]);
        $response->assertRedirect();

        $this->assertTrue(Provinsi::find('73')->status_aktif);
        $this->assertFalse(Provinsi::find('31')->status_aktif);
    }

    public function test_admin_can_apply_preset_toraja_for_kota(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.kota.batch-status'), [
            'action' => 'preset_toraja',
        ]);
        $response->assertRedirect();

        // Provinsi 73 should be active, 31 inactive
        $this->assertTrue(Provinsi::find('73')->status_aktif);
        $this->assertFalse(Provinsi::find('31')->status_aktif);

        // Kota 7318 and 7326 should be active, other kota inactive
        $this->assertTrue(Kota::find('7318')->status_aktif);
        $this->assertTrue(Kota::find('7326')->status_aktif);
        $this->assertFalse(Kota::find('7371')->status_aktif);
        $this->assertFalse(Kota::find('3171')->status_aktif);
    }

    public function test_wilayah_api_excludes_inactive_provinsi(): void
    {
        // Deactivate DKI Jakarta
        Provinsi::where('id', '31')->update(['status_aktif' => false]);

        $response = $this->actingAs($this->user)->getJson(route('wilayah.provinsi'));
        $response->assertOk();

        $results = $response->json('results');
        $ids = array_column($results, 'id');

        $this->assertContains('73', $ids);
        $this->assertNotContains('31', $ids);
    }

    public function test_wilayah_api_excludes_inactive_kota(): void
    {
        // Deactivate Makassar
        Kota::where('id', '7371')->update(['status_aktif' => false]);

        $response = $this->actingAs($this->user)->getJson(route('wilayah.kota', '73'));
        $response->assertOk();

        $results = $response->json('results');
        $ids = array_column($results, 'id');

        $this->assertContains('7318', $ids);
        $this->assertContains('7326', $ids);
        $this->assertNotContains('7371', $ids);
    }

    public function test_search_kelurahan_excludes_kelurahan_from_inactive_kota(): void
    {
        // Create Kelurahan in Toraja Utara (7326) and Makassar (7371)
        Kelurahan::create([
            'id' => '7326011001',
            'nama' => 'Desa Singki Toraja',
            'latitude' => 0,
            'longitude' => 0,
        ]);
        Kelurahan::create([
            'id' => '7371011001',
            'nama' => 'Kelurahan Pantai Makassar',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        // Deactivate Makassar
        Kota::where('id', '7371')->update(['status_aktif' => false]);

        $response = $this->actingAs($this->user)->getJson(route('wilayah.kelurahan-search', ['q' => 'Desa']));
        $response->assertOk();
        $this->assertCount(1, $response->json('results'));
        $this->assertSame('7326011001', $response->json('results.0.id'));

        // Search for Makassar kelurahan should yield nothing since city is deactivated
        $responseMakassar = $this->actingAs($this->user)->getJson(route('wilayah.kelurahan-search', ['q' => 'Pantai']));
        $responseMakassar->assertOk();
        $this->assertCount(0, $responseMakassar->json('results'));
    }
}
