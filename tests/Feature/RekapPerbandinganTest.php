<?php

namespace Tests\Feature;

use App\Models\Dusun;
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
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type') ?? '');
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition') ?? '');
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

    public function test_dusun_is_displayed_in_rekap_and_perbandingan_reports(): void
    {
        $user = User::factory()->create();
        $air = JenisBantuan::where('slug', 'air')->first();

        $dusun = Dusun::create([
            'kelurahan_id' => '7326011002',
            'nama' => 'Dusun Singki',
        ]);

        PenyaluranBantuan::create([
            'kode_transaksi' => 'BA-202610-7777',
            'jenis_bantuan_id' => $air->id,
            'provinsi_id' => '73',
            'kota_id' => '7326',
            'kecamatan_id' => '732601',
            'kelurahan_id' => '7326011002',
            'dusun_id' => $dusun->id,
            'nama_penerima' => 'Marten Palimbong',
            'jumlah_bantuan' => 5000,
            'satuan' => 'Liter',
            'tanggal_rencana' => '2026-10-15',
            'status' => 'TERSALURKAN',
        ]);

        // Rekap Index
        $rekapIndex = $this->actingAs($user)->get(route('rekap.index'));
        $rekapIndex->assertStatus(200);
        $rekapIndex->assertSee('Dusun Singki');

        // Rekap Cetak
        $rekapCetak = $this->actingAs($user)->get(route('rekap.cetak'));
        $rekapCetak->assertStatus(200);
        $rekapCetak->assertSee('Dusun Singki');

        // Perbandingan Index
        $perbandinganIndex = $this->actingAs($user)->get(route('rekap.perbandingan'));
        $perbandinganIndex->assertStatus(200);
        $perbandinganIndex->assertSee('Dusun Singki');

        // Perbandingan Cetak
        $perbandinganCetak = $this->actingAs($user)->get(route('rekap.perbandingan.cetak'));
        $perbandinganCetak->assertStatus(200);
        $perbandinganCetak->assertSee('Dusun Singki');

        // Perbandingan XLSX Export
        $excelResponse = $this->actingAs($user)->get(route('rekap.perbandingan.excel'));
        $excelResponse->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $excelResponse->headers->get('content-type') ?? '');
        $this->assertStringContainsString('.xlsx', $excelResponse->headers->get('content-disposition') ?? '');
        $content = $excelResponse->streamedContent();
        // File XLSX diawali dengan zip magic byte PK (0x50 0x4B)
        $this->assertStringStartsWith('PK', $content);
    }

    public function test_perbandingan_displays_detailed_rows_for_each_dusun_both_serviced_and_unserviced(): void
    {
        $user = User::factory()->create();
        $air = JenisBantuan::where('slug', 'air')->first();

        // Dusun 1: Sudah ada penyaluran
        $dusun1 = Dusun::create([
            'kelurahan_id' => '7326011002',
            'nama' => 'Dusun Batulelleng (Sudah)',
        ]);

        // Dusun 2: Belum ada penyaluran (blank spot / nihil)
        $dusun2 = Dusun::create([
            'kelurahan_id' => '7326011002',
            'nama' => 'Dusun Kandora (Belum)',
        ]);

        PenyaluranBantuan::create([
            'kode_transaksi' => 'BA-202610-1234',
            'jenis_bantuan_id' => $air->id,
            'provinsi_id' => '73',
            'kota_id' => '7326',
            'kecamatan_id' => '732601',
            'kelurahan_id' => '7326011002',
            'dusun_id' => $dusun1->id,
            'nama_penerima' => 'Warga Batulelleng',
            'jumlah_bantuan' => 3000,
            'satuan' => 'Liter',
            'tanggal_rencana' => '2026-10-20',
            'status' => 'TERSALURKAN',
        ]);

        $response = $this->actingAs($user)->get(route('rekap.perbandingan'));
        $response->assertStatus(200);

        // Dusun 1 (Sudah) harus tampil dengan data realisasinya
        $response->assertSee('Dusun Batulelleng (Sudah)');
        $response->assertSee('3.000 Liter');

        // Dusun 2 (Belum) harus tetap tampil dengan status Belum Tersentuh
        $response->assertSee('Dusun Kandora (Belum)');
        $response->assertSee('Belum Tersentuh');

        // Filter Hanya yang Belum Tersentuh
        $responseBlank = $this->actingAs($user)->get(route('rekap.perbandingan', ['filter_status' => 'belum_tersentuh']));
        $responseBlank->assertStatus(200);
        $responseBlank->assertSee('Dusun Kandora (Belum)');
        $responseBlank->assertDontSee('Dusun Batulelleng (Sudah)');
    }
}
