<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function __construct(
        protected VoiceTransactionParser $parser
    ) {}

    protected function botToken(): string
    {
        return (string) config('telegram.bot_token', '');
    }

    protected function baseUrl(): string
    {
        $base = rtrim(config('telegram.api_base_url', 'https://api.telegram.org'), '/');

        return "{$base}/bot{$this->botToken()}";
    }

    public function isConfigured(): bool
    {
        return ! empty($this->botToken());
    }

    /**
     * Send a text message to a Telegram chat.
     */
    public function sendMessage(string|int $chatId, string $text, ?array $replyMarkup = null): ?array
    {
        if (! $this->isConfigured()) {
            Log::warning('TelegramBotService: Bot token is not configured.');

            return null;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = $replyMarkup;
        }

        $response = Http::post("{$this->baseUrl()}/sendMessage", $payload);

        // Fallback to plain text if Markdown parsing fails
        if (! $response->successful() && str_contains($response->body(), "can't parse entities")) {
            unset($payload['parse_mode']);
            $response = Http::post("{$this->baseUrl()}/sendMessage", $payload);
        }

        return $response->json();
    }

    /**
     * Fetch unread updates via long polling.
     *
     * @return array<int, array>
     */
    public function getUpdates(?int $offset = null, int $timeout = 30): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $params = [
            'timeout' => $timeout,
        ];

        if ($offset !== null) {
            $params['offset'] = $offset;
        }

        $response = Http::timeout($timeout + 5)->get("{$this->baseUrl()}/getUpdates", $params);

        if ($response->successful()) {
            return $response->json('result', []);
        }

        Log::error('TelegramBotService: Failed to get updates', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [];
    }

    /**
     * Set webhook for production deployment.
     */
    public function setWebhook(string $url, ?string $secretToken = null): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'description' => 'Bot token not configured'];
        }

        $payload = ['url' => $url];
        if (! empty($secretToken)) {
            $payload['secret_token'] = $secretToken;
        }

        $response = Http::post("{$this->baseUrl()}/setWebhook", $payload);

        return $response->json() ?? [];
    }

    /**
     * Delete webhook (necessary before using long polling).
     */
    public function deleteWebhook(): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'description' => 'Bot token not configured'];
        }

        $response = Http::post("{$this->baseUrl()}/deleteWebhook");

        return $response->json() ?? [];
    }

    /**
     * Handle an incoming update (from polling or webhook).
     */
    public function handleUpdate(array $update): void
    {
        $message = $update['message'] ?? null;
        if (! $message) {
            return;
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        if ($chatId === '') {
            return;
        }

        // Handle voice messages
        if (isset($message['voice']) || isset($message['audio'])) {
            $this->sendMessage(
                $chatId,
                "🎙️ *Pesan suara diterima!*\n\nUntuk saat ini, Anda dapat mencatat lewat suara di Telegram menggunakan fitur *Dikte / Speech-to-Text* bawaan keyboard HP Anda (tekan ikon mic pada keyboard saat mengetik pesan), atau gunakan menu *Input Suara* di aplikasi web KeySaku."
            );

            return;
        }

        $text = trim((string) ($message['text'] ?? ''));
        if ($text === '') {
            return;
        }

        // Handle Commands
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $text);

            return;
        }

        // Handle Natural Language Transaction
        $this->handleTransactionText($chatId, $text);
    }

    protected function handleCommand(string $chatId, string $text): void
    {
        $parts = explode(' ', $text, 2);
        $command = strtolower($parts[0]);
        // Strip @bot_username if present (e.g. /start@Keysaku_bot)
        if (str_contains($command, '@')) {
            $command = explode('@', $command)[0];
        }

        $param = trim($parts[1] ?? '');

        switch ($command) {
            case '/start':
                $this->handleStartCommand($chatId, $param);
                break;

            case '/saldo':
                $this->handleSaldoCommand($chatId);
                break;

            case '/bantuan':
            case '/help':
                $this->handleHelpCommand($chatId);
                break;

            case '/batal':
            case '/undo':
                $this->handleBatalCommand($chatId);
                break;

            case '/unlink':
            case '/putus':
                $this->handleUnlinkCommand($chatId);
                break;

            default:
                $this->sendMessage(
                    $chatId,
                    'Perintah tidak dikenali. Ketik /bantuan untuk melihat panduan penggunaan Bot KeySaku.'
                );
                break;
        }
    }

    protected function handleStartCommand(string $chatId, string $token): void
    {
        if (! empty($token)) {
            $user = User::where('telegram_link_token', $token)->first();

            if ($user) {
                $user->linkTelegramChat($chatId);

                $this->sendMessage(
                    $chatId,
                    "🎉 *Berhasil Terhubung!*\n\nHalo, *{$user->name}*! Akun KeySaku Anda berhasil disambungkan dengan bot ini.\n\nSekarang Anda bisa langsung mencatat pengeluaran atau pemasukan secepat kirim pesan chat!\n\n*Contoh catatan:*\n• `Makan siang 35.000`\n• `Kopi susu 25rb kategori hiburan`\n• `Gaji bulanan 8jt`\n\nKetik /saldo untuk cek saldo atau /bantuan untuk melihat semua perintah."
                );

                return;
            }

            $this->sendMessage(
                $chatId,
                "⚠️ *Token tidak valid atau sudah kedaluwarsa.*\n\nSilakan buka aplikasi KeySaku di menu *Pengaturan > Akun Telegram* untuk mendapatkan tautan atau token terbaru."
            );

            return;
        }

        $user = User::where('telegram_chat_id', $chatId)->first();
        if ($user) {
            $this->sendMessage(
                $chatId,
                "Halo, *{$user->name}*! Akun Telegram Anda sudah terhubung ke KeySaku.\n\nAnda bisa langsung mencatat transaksi (misal: `Makan siang 35rb`) atau ketik /saldo untuk cek saldo."
            );

            return;
        }

        $botUsername = config('telegram.bot_username', 'Keysaku_bot');
        $this->sendMessage(
            $chatId,
            "👋 *Selamat datang di Bot KeySaku!*\n\nAkun Telegram Anda belum terhubung ke aplikasi KeySaku.\n\n*Cara menghubungkan:*\n1. Buka aplikasi KeySaku di HP atau browser Anda.\n2. Masuk ke menu *Pengaturan > Akun Telegram*.\n3. Klik tombol *Hubungkan ke Telegram* atau kirim token yang tertera dengan format:\n`/start <token_anda>`"
        );
    }

    protected function handleSaldoCommand(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (! $user) {
            $this->promptUnlinked($chatId);

            return;
        }

        $accounts = $user->household?->accounts()->get() ?? collect();

        if ($accounts->isEmpty()) {
            $this->sendMessage($chatId, '⚠️ Belum ada akun rekening/dompet yang terdaftar di KeySaku Anda.');

            return;
        }

        $total = 0;
        $lines = [];

        foreach ($accounts as $account) {
            $balance = (float) $account->balance;
            $total += $balance;
            $formatted = number_format($balance, 0, ',', '.');
            $lines[] = "• *{$account->name}:* Rp {$formatted}";
        }

        $formattedTotal = number_format($total, 0, ',', '.');
        $accountList = implode("\n", $lines);

        $this->sendMessage(
            $chatId,
            "💰 *Saldo Akun KeySaku*\n\n{$accountList}\n\n━━━━━━━━━━━━━━━\n*Total Saldo:* Rp {$formattedTotal}"
        );
    }

    protected function handleBatalCommand(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (! $user) {
            $this->promptUnlinked($chatId);

            return;
        }

        // Look for the latest telegram-created transaction within 60 minutes
        $transaction = Transaction::where('user_id', $user->id)
            ->where('source', Transaction::SOURCE_TELEGRAM)
            ->where('created_at', '>=', now()->subMinutes(60))
            ->latest()
            ->first();

        if (! $transaction) {
            $this->sendMessage(
                $chatId,
                'ℹ️ Tidak ada transaksi dari Telegram dalam 60 menit terakhir yang dapat dibatalkan.'
            );

            return;
        }

        $note = $transaction->note;
        $formattedAmount = number_format((float) $transaction->amount, 0, ',', '.');
        $typeLabel = $transaction->type === Transaction::TYPE_INCOME ? 'Pemasukan' : 'Pengeluaran';

        // Delete transaction (TransactionObserver will restore account balance)
        $transaction->delete();

        $account = $transaction->account_id ? $transaction->account?->fresh() : null;
        $balanceInfo = $account ? "\nSaldo *{$account->name}* dipulihkan: Rp ".number_format((float) $account->balance, 0, ',', '.') : '';

        $this->sendMessage(
            $chatId,
            "🗑️ *Transaksi Dibatalkan!*\n\n• *Jenis:* {$typeLabel}\n• *Catatan:* {$note}\n• *Nominal:* Rp {$formattedAmount}{$balanceInfo}"
        );
    }

    protected function handleUnlinkCommand(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (! $user) {
            $this->sendMessage($chatId, 'Akun Telegram Anda memang belum terhubung ke KeySaku.');

            return;
        }

        $user->unlinkTelegramChat();

        $this->sendMessage(
            $chatId,
            "🔌 *Koneksi Diputus*\n\nAkun Telegram Anda telah diputuskan dari KeySaku. Anda tidak akan menerima notifikasi atau mencatat via bot ini lagi sampai dihubungkan kembali."
        );
    }

    protected function handleHelpCommand(string $chatId): void
    {
        $this->sendMessage(
            $chatId,
            "📖 *Panduan Penggunaan Bot KeySaku*\n\nAnda bisa langsung mencatat pengeluaran atau pemasukan secepat mengirim chat biasa!\n\n*Contoh Pengeluaran:*\n• `makan siang 35000`\n• `kopi 25rb kategori hiburan`\n• `beli bensin 50.000 kemarin`\n• `belanja dapur 150rb`\n\n*Contoh Pemasukan:*\n• `gaji bulanan 8.000.000`\n• `terima bonus proyek 500rb`\n\n*Perintah Tersedia:*\n• /saldo - Cek saldo seluruh dompet & rekening\n• /batal - Batalkan transaksi terakhir jika salah ketik\n• /bantuan - Tampilkan pesan panduan ini\n• /putus - Putuskan hubungan akun dari bot ini"
        );
    }

    protected function handleTransactionText(string $chatId, string $text): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (! $user) {
            $this->promptUnlinked($chatId);

            return;
        }

        $household = $user->household;
        if (! $household) {
            $this->sendMessage($chatId, '⚠️ Akun Anda belum memiliki keluarga/household di KeySaku.');

            return;
        }

        $categories = $household->categories()->get();
        $draft = $this->parser->parse($text, $categories);

        $amount = (float) ($draft['amount'] ?? 0);
        if ($amount <= 0) {
            $this->sendMessage(
                $chatId,
                "⚠️ *Nominal belum terdeteksi dari pesan Anda.*\n\nPastikan menyertakan nominal transaksi, misalnya:\n• `makan siang 35.000`\n• `kopi susu 25rb`\n• `bensin 50rb`\n\nKetik /bantuan untuk contoh format lainnya."
            );

            return;
        }

        $type = $draft['type'] ?? Transaction::TYPE_EXPENSE;
        $categoryId = $draft['category_id'] ?? null;
        $category = null;

        if ($categoryId) {
            $category = $categories->firstWhere('id', $categoryId);
        }

        // Fallback category if not recognized
        if (! $category) {
            $matchingCategories = $categories->where('type', $type);

            $category = $matchingCategories->first(function (Category $c) {
                $lower = strtolower($c->name);

                return str_contains($lower, 'lain') || str_contains($lower, 'umum');
            }) ?? $matchingCategories->first();
        }

        if (! $category) {
            $this->sendMessage(
                $chatId,
                '⚠️ Belum ada kategori untuk transaksi ini di KeySaku Anda. Silakan tambahkan kategori terlebih dahulu di aplikasi web KeySaku.'
            );

            return;
        }

        $account = $household->accounts()->first();
        $note = trim((string) ($draft['note'] ?? ''));
        if ($note === '') {
            $note = $category->name;
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'household_id' => $household->id,
            'category_id' => $category->id,
            'account_id' => $account?->id,
            'type' => $type,
            'amount' => $amount,
            'transaction_date' => $draft['transaction_date'] ?? now()->toDateString(),
            'note' => $note,
            'source' => Transaction::SOURCE_TELEGRAM,
            'raw_voice_text' => $text,
        ]);

        $formattedAmount = number_format($amount, 0, ',', '.');
        $typeLabel = $type === Transaction::TYPE_INCOME ? 'Pemasukan' : 'Pengeluaran';
        $accountName = $account ? $account->name : 'Kas';
        $newBalance = $account ? 'Rp '.number_format((float) $account->fresh()->balance, 0, ',', '.') : '-';
        $dateFormatted = $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : date('d/m/Y');

        $this->sendMessage(
            $chatId,
            "✅ *{$typeLabel} Berhasil Dicatat!*\n\n• *Catatan:* {$note}\n• *Nominal:* Rp {$formattedAmount}\n• *Kategori:* {$category->name}\n• *Akun:* {$accountName}\n• *Tanggal:* {$dateFormatted}\n\n💰 *Sisa Saldo {$accountName}:* {$newBalance}\n\n_(Salah catat? Ketik /batal untuk membatalkan)_"
        );
    }

    protected function promptUnlinked(string $chatId): void
    {
        $this->sendMessage(
            $chatId,
            "🔒 *Akun Belum Terhubung*\n\nAkun Telegram ini belum terhubung ke KeySaku.\n\nSilakan buka menu *Pengaturan > Akun Telegram* di aplikasi KeySaku Anda, lalu klik tautan atau kirim perintah:\n`/start <token_anda>`"
        );
    }
}
