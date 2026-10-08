<?php

namespace Tests\Feature;

use App\Models\JenisBantuan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\PenyaluranBantuan;
use App\Models\Provinsi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BantuanAirTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        JenisBantuan::firstOrCreate(
            ['slug' => 'air'],
            ['nama' => 'Air Bersih', 'satuan' => 'Liter', 'status_aktif' => true]
        );

        Provinsi::firstOrCreate(
            ['id' => '73'],
            ['nama' => 'Sulawesi Selatan']
        );

        Kota::firstOrCreate(
            ['id' => '7326'],
            ['nama' => 'Kabupaten Toraja Utara']
        );
    }

    public function test_authenticated_user_can_access_bantuan_air_index_with_action_button(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('bantuan.air.index'));

        $response->assertStatus(200);
        $response->assertSee(route('bantuan.air.create'));
        $response->assertSee('Catat Bantuan Air');
    }

    public function test_authenticated_user_can_access_bantuan_air_create_page_with_default_regions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('bantuan.air.create'));

        $response->assertStatus(200);
        $response->assertSee('Catat Penyaluran Bantuan Air');
        $response->assertSee('Sulawesi Selatan');
        $response->assertSee('Kabupaten Toraja Utara');
    }

    public function test_authenticated_user_can_view_detail_and_quick_update_status(): void
    {
        $user = User::factory()->create();
        $air = JenisBantuan::where('slug', 'air')->first();

        Kelurahan::firstOrCreate(
            ['id' => '7326011002'],
            ['nama' => 'Rantepao', 'latitude' => -2.97566, 'longitude' => 119.89841]
        );

        $penyaluran = PenyaluranBantuan::create([
            'kode_transaksi' => 'BA-202610-9999',
            'jenis_bantuan_id' => $air->id,
            'provinsi_id' => '73',
            'kota_id' => '7326',
            'kecamatan_id' => '732601',
            'kelurahan_id' => '7326011002',
            'alamat_detail' => 'Depan Gereja Rantepao',
            'latitude' => -2.97566,
            'longitude' => 119.89841,
            'nama_penerima' => 'Yohanes Pongtiku',
            'kontak_penerima' => '081234567890',
            'jumlah_kk' => 30,
            'jumlah_jiwa' => 120,
            'jumlah_bantuan' => 5000,
            'satuan' => 'Liter',
            'tanggal_rencana' => '2026-10-10',
            'status' => 'RENCANA',
            'nomor_armada' => 'DP 8123 TA',
            'nama_petugas' => 'Markus',
        ]);

        // View detail page
        $response = $this->actingAs($user)->get(route('bantuan.air.show', $penyaluran->id));
        $response->assertStatus(200);
        $response->assertSee('BA-202610-9999');
        $response->assertSee('Yohanes Pongtiku');
        $response->assertSee('5.000');
        $response->assertSee('Cetak Lembar Serah Terima');
        $response->assertSee('Buka Navigasi di Google Maps');

        // Quick status update to PROSES
        $responsePatch = $this->actingAs($user)->patch(route('bantuan.air.status', $penyaluran->id), [
            'status' => 'PROSES',
        ]);
        $responsePatch->assertRedirect();
        $this->assertEquals('PROSES', $penyaluran->fresh()->status);
    }

    public function test_authenticated_user_can_access_bantuan_air_cetak_bast(): void
    {
        $user = User::factory()->create();
        $air = JenisBantuan::where('slug', 'air')->first();

        Kelurahan::firstOrCreate(
            ['id' => '7326011002'],
            ['nama' => 'Rantepao', 'latitude' => -2.97566, 'longitude' => 119.89841]
        );

        $penyaluran = PenyaluranBantuan::create([
            'kode_transaksi' => 'BA-202610-8888',
            'jenis_bantuan_id' => $air->id,
            'provinsi_id' => '73',
            'kota_id' => '7326',
            'kecamatan_id' => '732601',
            'kelurahan_id' => '7326011002',
            'alamat_detail' => 'Dusun Karassik',
            'latitude' => -2.97566,
            'longitude' => 119.89841,
            'nama_penerima' => 'Martha Rante',
            'kontak_penerima' => '081299988877',
            'jumlah_kk' => 45,
            'jumlah_jiwa' => 180,
            'jumlah_bantuan' => 10000,
            'satuan' => 'Liter',
            'tanggal_rencana' => '2026-10-12',
            'tanggal_penyaluran' => '2026-10-12',
            'status' => 'TERSALURKAN',
            'nomor_armada' => 'DP 9999 TA',
            'nama_petugas' => 'Petrus Salu',
        ]);

        $response = $this->actingAs($user)->get(route('bantuan.air.cetak', $penyaluran->id));

        $response->assertStatus(200);
        $response->assertSee('BERITA ACARA SERAH TERIMA (BAST)');
        $response->assertSee('BA-202610-8888');
        $response->assertSee('Martha Rante');
        $response->assertSee('10.000 Liter');
        $response->assertSee('Petrus Salu');
        $response->assertSee('PIHAK PERTAMA');
        $response->assertSee('PIHAK KEDUA');
    }
}
