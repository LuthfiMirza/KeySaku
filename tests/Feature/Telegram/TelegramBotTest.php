<?php

namespace Tests\Feature\Telegram;

use App\Models\Account;
use App\Models\Category;
use App\Models\Household;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramBotTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Household $household;

    protected Account $account;

    protected Category $expenseCategory;

    protected Category $incomeCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*/sendMessage*' => Http::response(['ok' => true, 'result' => ['message_id' => 123]], 200),
            '*' => Http::response(['ok' => true, 'result' => ['message_id' => 123]], 200),
        ]);

        $this->user = User::factory()->create();
        $this->household = $this->user->household;

        $this->account = Account::create([
            'household_id' => $this->household->id,
            'user_id' => $this->user->id,
            'name' => 'Dompet Kas',
            'type' => Account::TYPE_CASH,
            'balance' => 500000,
        ]);

        $this->expenseCategory = $this->household->categories()
            ->where('type', Category::TYPE_EXPENSE)
            ->first() ?? Category::create([
                'household_id' => $this->household->id,
                'name' => 'Makanan & Minuman',
                'type' => Category::TYPE_EXPENSE,
                'icon' => '🍔',
                'color' => '#f59e0b',
            ]);

        $this->incomeCategory = $this->household->categories()
            ->where('type', Category::TYPE_INCOME)
            ->first() ?? Category::create([
                'household_id' => $this->household->id,
                'name' => 'Gaji',
                'type' => Category::TYPE_INCOME,
                'icon' => '💵',
                'color' => '#10b981',
            ]);

        config()->set('telegram.bot_token', 'test_fake_token_123');
        config()->set('telegram.bot_username', 'Keysaku_bot');
    }

    public function test_user_can_link_telegram_account_via_start_token(): void
    {
        $token = $this->user->generateTelegramLinkToken();

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 1,
            'message' => [
                'message_id' => 101,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => "/start {$token}",
            ],
        ]);

        $this->user->refresh();
        $this->assertEquals('999888', $this->user->telegram_chat_id);
        $this->assertTrue($this->user->isTelegramLinked());
    }

    public function test_linked_user_can_record_expense_via_text(): void
    {
        $this->user->linkTelegramChat('999888');

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 2,
            'message' => [
                'message_id' => 102,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => 'makan siang 35.000',
            ],
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 35000,
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);

        $this->account->refresh();
        $this->assertEquals(465000, $this->account->balance);
    }

    public function test_linked_user_can_record_income_via_text(): void
    {
        $this->user->linkTelegramChat('999888');

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 3,
            'message' => [
                'message_id' => 103,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => 'gaji bulanan 2.000.000',
            ],
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'type' => Transaction::TYPE_INCOME,
            'amount' => 2000000,
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);

        $this->account->refresh();
        $this->assertEquals(2500000, $this->account->balance);
    }

    public function test_linked_user_can_undo_latest_transaction_via_batal(): void
    {
        $this->user->linkTelegramChat('999888');

        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 50000,
            'transaction_date' => now()->toDateString(),
            'note' => 'Beli bensin salah ketik',
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);

        $this->account->refresh();
        $this->assertEquals(450000, $this->account->balance);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 4,
            'message' => [
                'message_id' => 104,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => '/batal',
            ],
        ]);

        $this->assertSoftDeleted($transaction);
        $this->account->refresh();
        $this->assertEquals(500000, $this->account->balance);
    }

    public function test_linked_user_can_unlink_via_bot(): void
    {
        $this->user->linkTelegramChat('999888');

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 5,
            'message' => [
                'message_id' => 105,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => '/unlink',
            ],
        ]);

        $this->user->refresh();
        $this->assertNull($this->user->telegram_chat_id);
    }

    public function test_webhook_endpoint_processes_update(): void
    {
        $this->user->linkTelegramChat('999888');

        $response = $this->postJson(route('telegram.webhook'), [
            'update_id' => 6,
            'message' => [
                'message_id' => 106,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => 'kopi 20rb',
            ],
        ]);

        $response->assertOk();
        $response->assertJson(['ok' => true]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 20000,
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);
    }

    public function test_settings_telegram_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.telegram'));

        $response->assertOk();
        $response->assertSee('Integrasi Telegram Bot');
        $response->assertSee('Keysaku_bot');
    }

    public function test_user_can_unlink_telegram_from_web_settings(): void
    {
        $this->user->linkTelegramChat('999888');

        $response = $this->actingAs($this->user)->post(route('settings.telegram.unlink'));

        $response->assertRedirect();
        $this->user->refresh();
        $this->assertNull($this->user->telegram_chat_id);
    }

    public function test_telegram_poll_command_fails_without_token(): void
    {
        config()->set('telegram.bot_token', '');

        $this->artisan('telegram:poll', ['--once' => true])
            ->expectsOutput('TELEGRAM_BOT_TOKEN is not configured in .env file.')
            ->assertExitCode(1);
    }

    public function test_telegram_poll_command_processes_updates_once(): void
    {
        $this->user->linkTelegramChat('999888');

        Http::swap(new Factory);
        Http::fake([
            '*getUpdates*' => Http::response([
                'ok' => true,
                'result' => [
                    [
                        'update_id' => 99,
                        'message' => [
                            'message_id' => 199,
                            'chat' => ['id' => 999888],
                            'from' => ['first_name' => 'Luthfi'],
                            'text' => 'bensin 50rb',
                        ],
                    ],
                ],
            ], 200),
            '*sendMessage*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 200],
            ], 200),
        ]);

        $this->artisan('telegram:poll', ['--once' => true, '--timeout' => 1])
            ->assertSuccessful();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 50000,
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);
    }

    public function test_telegram_webhook_command_info(): void
    {
        Http::fake([
            '*getWebhookInfo*' => Http::response([
                'ok' => true,
                'result' => [
                    'url' => 'https://example.com/webhook',
                    'pending_update_count' => 0,
                ],
            ], 200),
        ]);

        $this->artisan('telegram:webhook', ['action' => 'info'])
            ->assertSuccessful();
    }

    public function test_telegram_webhook_command_set(): void
    {
        Http::fake([
            '*setWebhook*' => Http::response([
                'ok' => true,
                'description' => 'Webhook was set',
            ], 200),
        ]);

        $this->artisan('telegram:webhook', ['action' => 'set', '--url' => 'https://example.com/hook'])
            ->assertSuccessful();
    }

    public function test_telegram_webhook_command_delete(): void
    {
        Http::fake([
            '*deleteWebhook*' => Http::response([
                'ok' => true,
                'description' => 'Webhook was deleted',
            ], 200),
        ]);

        $this->artisan('telegram:webhook', ['action' => 'delete'])
            ->assertSuccessful();
    }

    public function test_linked_user_can_get_rekap_overview(): void
    {
        $this->user->linkTelegramChat('999888');

        Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 150000,
            'transaction_date' => now()->toDateString(),
            'note' => 'Belanja mingguan',
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 10,
            'message' => [
                'message_id' => 110,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => '/rekap',
            ],
        ]);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Rekap Keuangan')
                && str_contains($request['text'], 'Pengeluaran');
        });
    }

    public function test_keyboard_shortcut_routes_correctly(): void
    {
        $this->user->linkTelegramChat('999888');

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 11,
            'message' => [
                'message_id' => 111,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => '💰 Cek Saldo',
            ],
        ]);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Saldo Akun KeySaku');
        });
    }

    public function test_voice_note_without_api_key_returns_instructions(): void
    {
        $this->user->linkTelegramChat('999888');

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 12,
            'message' => [
                'message_id' => 112,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'voice' => [
                    'file_id' => 'fake_voice_file_id',
                    'duration' => 3,
                ],
            ],
        ]);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Pesan suara diterima');
        });
    }

    public function test_voice_note_with_gemini_transcription(): void
    {
        $this->user->linkTelegramChat('999888');
        config()->set('telegram.gemini_api_key', 'fake_gemini_key');

        Http::swap(new Factory);
        Http::fake([
            '*getFile*' => Http::response([
                'ok' => true,
                'result' => [
                    'file_path' => 'voice/file_123.oga',
                ],
            ], 200),
            '*file/bot*/*' => Http::response('fake_audio_binary', 200),
            '*generativelanguage.googleapis.com*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'beli bensin 40.000'],
                            ],
                        ],
                    ],
                ],
            ], 200),
            '*sendMessage*' => Http::response(['ok' => true, 'result' => ['message_id' => 124]], 200),
        ]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 13,
            'message' => [
                'message_id' => 113,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'voice' => [
                    'file_id' => 'fake_voice_file_id',
                    'duration' => 3,
                ],
            ],
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 40000,
            'source' => Transaction::SOURCE_VOICE,
        ]);
    }
}
