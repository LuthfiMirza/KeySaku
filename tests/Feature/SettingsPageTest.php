<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_shows_active_and_disabled_items(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings'))
            ->assertOk()
            ->assertSee('Profil Pengguna')
            ->assertSee('Kategori Transaksi')
            ->assertSee('Notifikasi')
            ->assertSee('Keamanan')
            ->assertSee('Pasang di Layar Utama')
            ->assertSee('Mata Uang')
            ->assertSee('Bahasa')
            ->assertSee('Belum tersedia di versi ini')
            ->assertSee('Nonaktif')
            ->assertDontSee('NANTI')
            ->assertDontSee('Segera');
    }

    public function test_notification_settings_page_is_authenticated_and_informational(): void
    {
        $user = User::factory()->create();

        $this->get(route('settings.notifications'))->assertRedirect(route('login', absolute: false));

        $this->actingAs($user)
            ->get(route('settings.notifications'))
            ->assertOk()
            ->assertSee('Notifikasi')
            ->assertSee('Pengingat belum aktif di versi ini.')
            ->assertSee('Pengingat Anggaran')
            ->assertSee('Pengingat Target Tabungan')
            ->assertSee('belum mengirim email, SMS, atau push notification');
    }

    public function test_security_settings_page_links_to_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings.security'))
            ->assertOk()
            ->assertSee('Keamanan')
            ->assertSee('Pengaturan akun dan password dikelola dari halaman profil.')
            ->assertSee('Buka Profil');
    }

    public function test_install_settings_page_shows_instruction_only_content(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings.install'))
            ->assertOk()
            ->assertSee('Pasang di Layar Utama')
            ->assertSee('Android Chrome')
            ->assertSee('iPhone Safari')
            ->assertSee('Desktop')
            ->assertSee('mode PWA belum memakai service worker');
    }
}
