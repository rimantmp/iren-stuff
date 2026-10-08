<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramBantuanTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_coming_soon_modules(): void
    {
        $user = User::factory()->create();

        // Sembako
        $responseSembako = $this->actingAs($user)->get(route('bantuan.sembako'));
        $responseSembako->assertStatus(200);
        $responseSembako->assertSee('Modul Paket Sembako Segera Hadir');
        $responseSembako->assertSee('Menunggu Brief Data');

        // Tunai Gereja
        $responseTunai = $this->actingAs($user)->get(route('bantuan.tunai-gereja'));
        $responseTunai->assertStatus(200);
        $responseTunai->assertSee('Modul Bantuan Tunai Gereja Segera Hadir');
        $responseTunai->assertSee('Menunggu Brief Data');

        // Pengadaan
        $responsePengadaan = $this->actingAs($user)->get(route('bantuan.pengadaan'));
        $responsePengadaan->assertStatus(200);
        $responsePengadaan->assertSee('Modul Pengadaan Sarana Segera Hadir');
        $responsePengadaan->assertSee('Menunggu Brief Data');
    }
}
