<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Kelurahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDusunTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Kelurahan $kelurahan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create dummy kelurahan
        $this->kelurahan = new Kelurahan;
        $this->kelurahan->id = '7326011001';
        $this->kelurahan->nama = 'Singki';
        $this->kelurahan->latitude = -2.97566;
        $this->kelurahan->longitude = 119.89841;
        $this->kelurahan->save();
    }

    public function test_unauthenticated_user_cannot_access_dusun_page(): void
    {
        $response = $this->get(route('master.dusun'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_dusun_page(): void
    {
        Dusun::create([
            'kelurahan_id' => $this->kelurahan->id,
            'nama' => 'Dusun Karassik',
            'rt' => '01',
            'rw' => '02',
            'kepala_dusun' => 'Bapak Yohanes',
            'latitude' => -2.9760,
            'longitude' => 119.8990,
        ]);

        $response = $this->actingAs($this->user)->get(route('master.dusun'));

        $response->assertStatus(200);
        $response->assertSee('Master Data Dusun');
        $response->assertSee('Dusun Karassik');
        $response->assertSee('Singki');
    }

    public function test_admin_can_store_new_dusun(): void
    {
        $payload = [
            'kelurahan_id' => $this->kelurahan->id,
            'nama' => 'Dusun Sukomulyo',
            'rt' => 'RT 03',
            'rw' => 'RW 01',
            'kepala_dusun' => 'Marthen L.',
            'latitude' => -2.9800,
            'longitude' => 119.9000,
            'keterangan' => 'Akses jalan cor beton',
        ];

        $response = $this->actingAs($this->user)->post(route('master.dusun.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('t_dusun', [
            'nama' => 'Dusun Sukomulyo',
            'kelurahan_id' => '7326011001',
            'kepala_dusun' => 'Marthen L.',
        ]);
    }

    public function test_store_dusun_validation_requires_nama_and_kelurahan(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.dusun.store'), [
            'nama' => '',
            'kelurahan_id' => '',
        ]);

        $response->assertSessionHasErrors(['nama', 'kelurahan_id']);
    }

    public function test_admin_can_update_dusun(): void
    {
        $dusun = Dusun::create([
            'kelurahan_id' => $this->kelurahan->id,
            'nama' => 'Dusun Awal',
            'rt' => 'RT 01',
            'rw' => 'RW 01',
        ]);

        $payload = [
            'kelurahan_id' => $this->kelurahan->id,
            'nama' => 'Dusun Terupdate',
            'rt' => 'RT 02',
            'rw' => 'RW 02',
            'kepala_dusun' => 'Markus',
        ];

        $response = $this->actingAs($this->user)->put(route('master.dusun.update', $dusun->id), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('t_dusun', [
            'id' => $dusun->id,
            'nama' => 'Dusun Terupdate',
            'rt' => 'RT 02',
            'rw' => 'RW 02',
            'kepala_dusun' => 'Markus',
        ]);
    }

    public function test_admin_can_destroy_dusun(): void
    {
        $dusun = Dusun::create([
            'kelurahan_id' => $this->kelurahan->id,
            'nama' => 'Dusun Dihapus',
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.dusun.destroy', $dusun->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('t_dusun', [
            'id' => $dusun->id,
        ]);
    }

    public function test_ajax_search_kelurahan_and_get_dusun(): void
    {
        $dusun = Dusun::create([
            'kelurahan_id' => $this->kelurahan->id,
            'nama' => 'Dusun Ba\'tan',
        ]);

        // Search kelurahan
        $searchRes = $this->actingAs($this->user)->getJson(route('wilayah.kelurahan-search', ['q' => 'Singki']));
        $searchRes->assertStatus(200);
        $searchRes->assertJsonFragment(['id' => $this->kelurahan->id]);

        // Get dusun by kelurahan ID
        $dusunRes = $this->actingAs($this->user)->getJson(route('wilayah.dusun', $this->kelurahan->id));
        $dusunRes->assertStatus(200);
        $dusunRes->assertJsonFragment(['text' => 'Dusun Ba\'tan']);
    }

    public function test_admin_can_filter_dusun_by_kota_id(): void
    {
        // Kelurahan in Toraja Utara (7326)
        $dusunTorut = Dusun::create([
            'kelurahan_id' => $this->kelurahan->id,
            'nama' => 'Dusun Torut Karassik',
        ]);

        // Create Kelurahan in Tana Toraja (7318)
        $kelTator = Kelurahan::create([
            'id' => '7318011001',
            'nama' => 'Kelurahan Makale',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        $dusunTator = Dusun::create([
            'kelurahan_id' => $kelTator->id,
            'nama' => 'Dusun Tator Pantan',
        ]);

        // Filter by Toraja Utara (7326)
        $responseTorut = $this->actingAs($this->user)->get(route('master.dusun', ['kota_id' => '7326']));
        $responseTorut->assertOk();
        $responseTorut->assertSee('Dusun Torut Karassik');
        $responseTorut->assertDontSee('Dusun Tator Pantan');

        // Filter by Tana Toraja (7318)
        $responseTator = $this->actingAs($this->user)->get(route('master.dusun', ['kota_id' => '7318']));
        $responseTator->assertOk();
        $responseTator->assertSee('Dusun Tator Pantan');
        $responseTator->assertDontSee('Dusun Torut Karassik');
    }

    public function test_ajax_search_kelurahan_with_kota_id_parameter(): void
    {
        $kelTator = Kelurahan::create([
            'id' => '7318011002',
            'nama' => 'Lembang Batualu',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        // Search with kota_id=7326 (Toraja Utara) should only return Singki, not Batualu
        $searchRes = $this->actingAs($this->user)->getJson(route('wilayah.kelurahan-search', [
            'q' => 'Singki',
            'kota_id' => '7326',
        ]));
        $searchRes->assertOk();
        $searchRes->assertJsonFragment(['id' => $this->kelurahan->id]);

        // Search Batualu with kota_id=7326 should yield empty
        $searchWrong = $this->actingAs($this->user)->getJson(route('wilayah.kelurahan-search', [
            'q' => 'Batualu',
            'kota_id' => '7326',
        ]));
        $searchWrong->assertOk();
        $searchWrong->assertJsonCount(0, 'results');

        // Search Batualu with kota_id=7318 should return Batualu
        $searchRight = $this->actingAs($this->user)->getJson(route('wilayah.kelurahan-search', [
            'q' => 'Batualu',
            'kota_id' => '7318',
        ]));
        $searchRight->assertOk();
        $searchRight->assertJsonCount(1, 'results');
        $searchRight->assertJsonFragment(['id' => $kelTator->id]);
    }

    public function test_admin_can_filter_dusun_by_kecamatan_id(): void
    {
        // Kelurahan 7326011001 (Kecamatan 732601)
        $dusunKec1 = Dusun::create([
            'kelurahan_id' => $this->kelurahan->id,
            'nama' => 'Dusun Rantepao Satu',
        ]);

        // Create Kelurahan in another kecamatan 7326021001 (Kecamatan 732602)
        $kelKec2 = Kelurahan::create([
            'id' => '7326021001',
            'nama' => 'Kelurahan Tallunglipu',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        $dusunKec2 = Dusun::create([
            'kelurahan_id' => $kelKec2->id,
            'nama' => 'Dusun Tallunglipu Dua',
        ]);

        // Filter by Kecamatan 732601
        $resKec1 = $this->actingAs($this->user)->get(route('master.dusun', ['kecamatan_id' => '732601']));
        $resKec1->assertOk();
        $resKec1->assertSee('Dusun Rantepao Satu');
        $resKec1->assertDontSee('Dusun Tallunglipu Dua');

        // Filter by Kecamatan 732602
        $resKec2 = $this->actingAs($this->user)->get(route('master.dusun', ['kecamatan_id' => '732602']));
        $resKec2->assertOk();
        $resKec2->assertSee('Dusun Tallunglipu Dua');
        $resKec2->assertDontSee('Dusun Rantepao Satu');
    }

    public function test_ajax_search_kelurahan_with_kecamatan_id_parameter(): void
    {
        // Kelurahan in 732602
        $kelKec2 = Kelurahan::create([
            'id' => '7326021002',
            'nama' => 'Lembang Tagari',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        // Search Tagari with kecamatan_id=732601 should return 0 results
        $resWrong = $this->actingAs($this->user)->getJson(route('wilayah.kelurahan-search', [
            'q' => 'Tagari',
            'kecamatan_id' => '732601',
        ]));
        $resWrong->assertOk();
        $resWrong->assertJsonCount(0, 'results');

        // Search Tagari with kecamatan_id=732602 should return Tagari
        $resRight = $this->actingAs($this->user)->getJson(route('wilayah.kelurahan-search', [
            'q' => 'Tagari',
            'kecamatan_id' => '732602',
        ]));
        $resRight->assertOk();
        $resRight->assertJsonCount(1, 'results');
        $resRight->assertJsonFragment(['id' => $kelKec2->id]);
    }
}
