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
use Illuminate\Support\Facades\Cache;
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

    public function test_duplicate_update_is_deduplicated_by_service(): void
    {
        $this->user->linkTelegramChat('999888');

        $update = [
            'update_id' => 999111,
            'message' => [
                'message_id' => 456,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => 'beli bensin 40000',
            ],
        ];

        $service = app(TelegramBotService::class);

        // Process first time
        $service->handleUpdate($update);

        // Process duplicate
        $service->handleUpdate($update);

        // Must only be recorded once
        $this->assertEquals(1, Transaction::where('note', 'beli bensin')->count());
    }

    public function test_telegram_poll_command_warns_when_another_instance_running(): void
    {
        $lock = Cache::lock('telegram:poll:single_instance_lock', 60);
        $lock->get();

        try {
            $this->artisan('telegram:poll', ['--once' => true])
                ->expectsOutputToContain('Instance telegram:poll lain sudah berjalan aktif')
                ->assertSuccessful();
        } finally {
            $lock->release();
        }
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

    public function test_linked_user_can_get_rekap_harian(): void
    {
        $this->user->linkTelegramChat('999888');

        Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 45000,
            'transaction_date' => now()->toDateString(),
            'note' => 'Makan siang padang',
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 101,
            'message' => [
                'message_id' => 1101,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => '/hariini',
            ],
        ]);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Rekap Harian')
                && str_contains($request['text'], 'Makan siang padang')
                && str_contains($request['text'], '45.000');
        });
    }

    public function test_linked_user_can_get_rekap_mingguan(): void
    {
        $this->user->linkTelegramChat('999888');

        Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 85000,
            'transaction_date' => now()->toDateString(),
            'note' => 'Bensin motor mingguan',
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 102,
            'message' => [
                'message_id' => 1102,
                'chat' => ['id' => 999888],
                'from' => ['first_name' => 'Luthfi'],
                'text' => '/mingguini',
            ],
        ]);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Rekap Mingguan')
                && str_contains($request['text'], 'Pengeluaran Harian Pekan Ini')
                && str_contains($request['text'], '85.000');
        });
    }

    public function test_rekap_period_switcher_callback_query_edits_message(): void
    {
        $this->user->linkTelegramChat('999888');

        Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 50000,
            'transaction_date' => now()->toDateString(),
            'note' => 'Belanja tes',
            'source' => Transaction::SOURCE_TELEGRAM,
        ]);

        Http::swap(new Factory);
        Http::fake([
            '*answerCallbackQuery*' => Http::response(['ok' => true], 200),
            '*editMessageText*' => Http::response(['ok' => true], 200),
        ]);

        $service = app(TelegramBotService::class);

        // Test switch to daily
        $service->handleUpdate([
            'update_id' => 103,
            'callback_query' => [
                'id' => 'cb_query_daily',
                'data' => 'rekap:daily',
                'message' => [
                    'message_id' => 300,
                    'chat' => ['id' => 999888],
                ],
            ],
        ]);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'editMessageText')
                && str_contains($request['text'], 'Rekap Harian');
        });

        // Test switch to weekly
        $service->handleUpdate([
            'update_id' => 104,
            'callback_query' => [
                'id' => 'cb_query_weekly',
                'data' => 'rekap:weekly',
                'message' => [
                    'message_id' => 300,
                    'chat' => ['id' => 999888],
                ],
            ],
        ]);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'editMessageText')
                && str_contains($request['text'], 'Rekap Mingguan');
        });

        // Test switch to monthly
        $service->handleUpdate([
            'update_id' => 105,
            'callback_query' => [
                'id' => 'cb_query_monthly',
                'data' => 'rekap:monthly',
                'message' => [
                    'message_id' => 300,
                    'chat' => ['id' => 999888],
                ],
            ],
        ]);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'editMessageText')
                && str_contains($request['text'], 'Rekap Keuangan');
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
        config()->set('telegram.gemini_api_key', '');
        config()->set('telegram.groq_api_key', '');

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

    public function test_voice_note_with_gemini_fallback_model_on_first_model_failure(): void
    {
        $this->user->linkTelegramChat('999888');
        config()->set('telegram.gemini_api_key', 'fake_gemini_key');
        config()->set('telegram.gemini_model', 'gemini-non-existent');

        Http::swap(new Factory);
        Http::fake([
            '*getFile*' => Http::response([
                'ok' => true,
                'result' => [
                    'file_path' => 'voice/file_123.oga',
                ],
            ], 200),
            '*file/bot*/*' => Http::response('fake_audio_binary', 200),
            '*gemini-non-existent*' => Http::response([
                'error' => ['code' => 404, 'message' => 'Model not found'],
            ], 404),
            '*gemini-3.5-flash-lite*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'beli pulsa 50.000'],
                            ],
                        ],
                    ],
                ],
            ], 200),
            '*sendMessage*' => Http::response(['ok' => true, 'result' => ['message_id' => 125]], 200),
        ]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 14,
            'message' => [
                'message_id' => 114,
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
            'amount' => 50000,
            'source' => Transaction::SOURCE_VOICE,
        ]);
    }

    public function test_user_can_view_transaction_riwayat(): void
    {
        $this->user->linkTelegramChat('999888');

        Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 37000,
            'transaction_date' => now()->toDateString(),
            'note' => 'beli koper',
            'source' => Transaction::SOURCE_VOICE,
        ]);

        $capturedText = '';
        Http::swap(new Factory);
        Http::fake([
            '*sendMessage*' => function ($request) use (&$capturedText) {
                $capturedText = $request['text'] ?? '';

                return Http::response(['ok' => true, 'result' => ['message_id' => 126]], 200);
            },
        ]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 15,
            'message' => [
                'message_id' => 115,
                'chat' => ['id' => 999888],
                'text' => '/riwayat',
            ],
        ]);

        $this->assertStringContainsString('5 Transaksi Terakhir', $capturedText);
        $this->assertStringContainsString('beli koper', $capturedText);
        $this->assertStringContainsString('37.000', $capturedText);
    }

    public function test_user_can_delete_specific_transaction_by_id_via_hapus_command(): void
    {
        $this->user->linkTelegramChat('999888');

        $trx = Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 37000,
            'transaction_date' => now()->toDateString(),
            'note' => 'beli koper',
            'source' => Transaction::SOURCE_VOICE,
        ]);

        Http::swap(new Factory);
        Http::fake(['*sendMessage*' => Http::response(['ok' => true, 'result' => ['message_id' => 127]], 200)]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 16,
            'message' => [
                'message_id' => 116,
                'chat' => ['id' => 999888],
                'text' => "/hapus {$trx->id}",
            ],
        ]);

        $this->assertSoftDeleted('transactions', [
            'id' => $trx->id,
        ]);
    }

    public function test_user_can_edit_transaction_via_edit_command(): void
    {
        $this->user->linkTelegramChat('999888');

        $trx = Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 37000,
            'transaction_date' => now()->toDateString(),
            'note' => 'beli koper',
            'source' => Transaction::SOURCE_VOICE,
        ]);

        $capturedText = '';
        Http::swap(new Factory);
        Http::fake([
            '*sendMessage*' => function ($request) use (&$capturedText) {
                $capturedText = $request['text'] ?? '';

                return Http::response(['ok' => true, 'result' => ['message_id' => 128]], 200);
            },
        ]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 17,
            'message' => [
                'message_id' => 117,
                'chat' => ['id' => 999888],
                'text' => "/edit {$trx->id} beli kopi susu 45000",
            ],
        ]);

        $trx->refresh();
        $this->assertEquals('beli kopi susu', $trx->note);
        $this->assertEquals(45000, $trx->amount);
        $this->assertStringContainsString('Berhasil Diperbarui', $capturedText);
    }

    public function test_user_can_delete_transaction_via_callback_query(): void
    {
        $this->user->linkTelegramChat('999888');

        $trx = Transaction::create([
            'user_id' => $this->user->id,
            'household_id' => $this->household->id,
            'category_id' => $this->expenseCategory->id,
            'account_id' => $this->account->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 37000,
            'transaction_date' => now()->toDateString(),
            'note' => 'beli koper',
            'source' => Transaction::SOURCE_VOICE,
        ]);

        Http::swap(new Factory);
        Http::fake([
            '*answerCallbackQuery*' => Http::response(['ok' => true], 200),
            '*editMessageText*' => Http::response(['ok' => true], 200),
        ]);

        $service = app(TelegramBotService::class);
        $service->handleUpdate([
            'update_id' => 18,
            'callback_query' => [
                'id' => 'cb_query_999',
                'data' => "del_trx:{$trx->id}",
                'message' => [
                    'message_id' => 200,
                    'chat' => ['id' => 999888],
                ],
            ],
        ]);

        $this->assertSoftDeleted('transactions', [
            'id' => $trx->id,
        ]);
    }

    public function test_user_cannot_delete_or_edit_another_household_transaction(): void
    {
        $this->user->linkTelegramChat('999888');

        $otherUser = User::factory()->create();
        $otherCategory = $otherUser->household->categories()->first();
        $otherTrx = Transaction::create([
            'user_id' => $otherUser->id,
            'household_id' => $otherUser->household_id,
            'category_id' => $otherCategory->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 100000,
            'transaction_date' => now()->toDateString(),
            'note' => 'transaksi orang lain',
            'source' => Transaction::SOURCE_VOICE,
        ]);

        Http::swap(new Factory);
        Http::fake(['*sendMessage*' => Http::response(['ok' => true], 200)]);

        $service = app(TelegramBotService::class);

        // Attempt /hapus cross-household
        $service->handleUpdate([
            'update_id' => 19,
            'message' => [
                'message_id' => 118,
                'chat' => ['id' => 999888],
                'text' => "/hapus {$otherTrx->id}",
            ],
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $otherTrx->id,
            'deleted_at' => null,
        ]);

        // Attempt /edit cross-household
        $service->handleUpdate([
            'update_id' => 20,
            'message' => [
                'message_id' => 119,
                'chat' => ['id' => 999888],
                'text' => "/edit {$otherTrx->id} bajak 50000",
            ],
        ]);

        $otherTrx->refresh();
        $this->assertEquals(100000, $otherTrx->amount);
        $this->assertEquals('transaksi orang lain', $otherTrx->note);
    }
}
