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
            ->assertDontSee('Belum tersedia di versi ini')
            ->assertDontSee('Nonaktif')
            ->assertDontSee('NANTI')
            ->assertDontSee('Segera');
    }


    public function test_authenticated_user_can_open_currency_settings_page(): void
    {
        $user = User::factory()->create();

        $this->get(route('settings.currency'))->assertRedirect(route('login', absolute: false));

        $this->actingAs($user)
            ->get(route('settings.currency'))
            ->assertOk()
            ->assertSee('Mata Uang')
            ->assertSee('Pilih mata uang utama untuk tampilan nominal.')
            ->assertSee('IDR')
            ->assertSee('Rupiah Indonesia')
            ->assertSee('USD')
            ->assertSee('US Dollar')
            ->assertSee('Simpan Mata Uang');
    }

    public function test_authenticated_user_can_update_currency_to_idr_and_usd(): void
    {
        $user = User::factory()->create(['preferred_currency' => 'IDR']);

        $this->actingAs($user)
            ->patch(route('settings.currency.update'), ['preferred_currency' => 'USD'])
            ->assertRedirect();

        $this->assertSame('USD', $user->fresh()->preferred_currency);

        $this->actingAs($user)
            ->from(route('settings.currency'))
            ->patch(route('settings.currency.update'), ['preferred_currency' => 'IDR'])
            ->assertRedirect(route('settings.currency'));

        $this->assertSame('IDR', $user->fresh()->preferred_currency);
    }

    public function test_invalid_currency_is_rejected(): void
    {
        $user = User::factory()->create(['preferred_currency' => 'IDR']);

        $this->actingAs($user)
            ->from(route('settings.currency'))
            ->patch(route('settings.currency.update'), ['preferred_currency' => 'EUR'])
            ->assertRedirect(route('settings.currency'))
            ->assertSessionHasErrors('preferred_currency');

        $this->assertSame('IDR', $user->fresh()->preferred_currency);
    }

    public function test_authenticated_user_can_open_language_settings_page(): void
    {
        $user = User::factory()->create();

        $this->get(route('settings.language'))->assertRedirect(route('login', absolute: false));

        $this->actingAs($user)
            ->get(route('settings.language'))
            ->assertOk()
            ->assertSee('Bahasa')
            ->assertSee('Pilih bahasa tampilan aplikasi.')
            ->assertSee('id')
            ->assertSee('Indonesia')
            ->assertSee('en')
            ->assertSee('English')
            ->assertSee('Simpan Bahasa')
            ->assertSee('Terjemahan penuh aplikasi dapat diterapkan bertahap setelah preferensi bahasa aktif.');
    }

    public function test_authenticated_user_can_update_language_to_id_and_en(): void
    {
        $user = User::factory()->create(['language' => 'id']);

        $this->actingAs($user)
            ->patch(route('settings.language.update'), ['language' => 'en'])
            ->assertRedirect();

        $this->assertSame('en', $user->fresh()->language);

        $this->actingAs($user)
            ->from(route('settings.language'))
            ->patch(route('settings.language.update'), ['language' => 'id'])
            ->assertRedirect(route('settings.language'));

        $this->assertSame('id', $user->fresh()->language);
    }

    public function test_invalid_language_is_rejected(): void
    {
        $user = User::factory()->create(['language' => 'id']);

        $this->actingAs($user)
            ->from(route('settings.language'))
            ->patch(route('settings.language.update'), ['language' => 'fr'])
            ->assertRedirect(route('settings.language'))
            ->assertSessionHasErrors('language');

        $this->assertSame('id', $user->fresh()->language);
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
