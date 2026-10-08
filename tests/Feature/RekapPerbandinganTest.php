<?php

namespace Tests\Feature;

use App\Models\JenisBantuan;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\PenyaluranBantuan;
use App\Models\Provinsi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapPerbandinganTest extends TestCase
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

        Kecamatan::firstOrCreate(
            ['id' => '732601'],
            ['nama' => 'Rantepao']
        );

        Kelurahan::firstOrCreate(
            ['id' => '7326011002'],
            ['nama' => 'Rantepao', 'latitude' => -2.97566, 'longitude' => 119.89841]
        );
    }

    public function test_authenticated_user_can_view_laporan_perbandingan(): void
    {
        $user = User::factory()->create();
        $air = JenisBantuan::where('slug', 'air')->first();

        PenyaluranBantuan::create([
            'kode_transaksi' => 'BA-202610-0001',
            'jenis_bantuan_id' => $air->id,
            'provinsi_id' => '73',
            'kota_id' => '7326',
            'kecamatan_id' => '732601',
            'kelurahan_id' => '7326011002',
            'alamat_detail' => 'Kompleks Pasar Bolu',
            'nama_penerima' => 'Yohanes Pongtiku',
            'jumlah_kk' => 20,
            'jumlah_jiwa' => 80,
            'jumlah_bantuan' => 5000,
            'satuan' => 'Liter',
            'tanggal_rencana' => '2026-10-10',
            'status' => 'TERSALURKAN',
        ]);

        $response = $this->actingAs($user)->get(route('rekap.perbandingan'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Perbandingan Wilayah');
        $response->assertSee('Kabupaten Toraja Utara');
        $response->assertSee('Rantepao');
        $response->assertSee('Download Excel');
        $response->assertSee('Cetak / Download PDF');
        $response->assertSee('5.000 Liter');
    }

    public function test_authenticated_user_can_view_laporan_perbandingan_cetak(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('rekap.perbandingan.cetak'));

        $response->assertStatus(200);
        $response->assertSee('LAPORAN PERBANDINGAN TARGET DAN REALISASI PENYALURAN');
    }

    public function test_authenticated_user_can_download_laporan_perbandingan_excel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('rekap.perbandingan.excel'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type') ?? '');
    }

    public function test_authenticated_user_can_filter_by_status_tersalurkan_dan_belum(): void
    {
        $user = User::factory()->create();
        $air = JenisBantuan::where('slug', 'air')->first();

        // Kelurahan 1: Sudah ada transaksi
        PenyaluranBantuan::create([
            'kode_transaksi' => 'BA-202610-0001',
            'jenis_bantuan_id' => $air->id,
            'provinsi_id' => '73',
            'kota_id' => '7326',
            'kecamatan_id' => '732601',
            'kelurahan_id' => '7326011002',
            'alamat_detail' => 'Kompleks Pasar Bolu',
            'nama_penerima' => 'Yohanes Pongtiku',
            'jumlah_kk' => 20,
            'jumlah_jiwa' => 80,
            'jumlah_bantuan' => 5000,
            'satuan' => 'Liter',
            'tanggal_rencana' => '2026-10-10',
            'status' => 'TERSALURKAN',
        ]);

        // Kelurahan 2: Belum ada transaksi sama sekali (Blank spot)
        Kelurahan::firstOrCreate(
            ['id' => '7326011003'],
            ['nama' => 'Pantan Blank Spot', 'latitude' => -2.97, 'longitude' => 119.89]
        );

        // Test Filter: Sudah Tersalurkan
        $responseSudah = $this->actingAs($user)->get(route('rekap.perbandingan', ['filter_status' => 'sudah']));
        $responseSudah->assertStatus(200);
        $responseSudah->assertSee('Rantepao');
        $responseSudah->assertDontSee('Pantan Blank Spot');

        // Test Filter: Belum Tersentuh / Belum Tersalurkan
        $responseBelum = $this->actingAs($user)->get(route('rekap.perbandingan', ['filter_status' => 'belum_tersentuh']));
        $responseBelum->assertStatus(200);
        $responseBelum->assertSee('Pantan Blank Spot');
        $responseBelum->assertDontSee('5.000 Liter');
    }

    public function test_guest_cannot_access_laporan_perbandingan(): void
    {
        $response = $this->get(route('rekap.perbandingan'));
        $response->assertRedirect(route('login'));
    }
}
