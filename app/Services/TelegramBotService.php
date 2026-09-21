<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Household;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
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

    public function hasSpeechRecognition(): bool
    {
        return ! empty(config('telegram.gemini_api_key')) || ! empty(config('telegram.groq_api_key'));
    }

    /**
     * Default interactive bottom keyboard markup.
     */
    public function defaultKeyboard(): array
    {
        return [
            'keyboard' => [
                [['text' => '💰 Cek Saldo'], ['text' => '📊 Rekap Bulan Ini']],
                [['text' => '📋 Riwayat'], ['text' => '🗑️ Batalkan Transaksi']],
                [['text' => '❓ Bantuan']],
            ],
            'resize_keyboard' => true,
            'is_persistent' => true,
        ];
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
            'reply_markup' => $replyMarkup ?? $this->defaultKeyboard(),
        ];

        $response = Http::post("{$this->baseUrl()}/sendMessage", $payload);

        // Fallback to plain text if Markdown parsing fails
        if (! $response->successful() && str_contains($response->body(), "can't parse entities")) {
            unset($payload['parse_mode']);
            $response = Http::post("{$this->baseUrl()}/sendMessage", $payload);
        }

        return $response->json();
    }

    /**
     * Answer a callback query from an inline keyboard button.
     */
    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null, bool $showAlert = false): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $payload = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => $showAlert,
        ];

        if ($text !== null) {
            $payload['text'] = $text;
        }

        $response = Http::post("{$this->baseUrl()}/answerCallbackQuery", $payload);

        return $response->json();
    }

    /**
     * Edit message text in a Telegram chat.
     */
    public function editMessageText(string|int $chatId, int $messageId, string $text, ?array $replyMarkup = null): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = $replyMarkup;
        }

        $response = Http::post("{$this->baseUrl()}/editMessageText", $payload);

        if (! $response->successful() && str_contains($response->body(), "can't parse entities")) {
            unset($payload['parse_mode']);
            $response = Http::post("{$this->baseUrl()}/editMessageText", $payload);
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
     * Download binary audio file from Telegram.
     */
    public function downloadTelegramFile(string $fileId): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = Http::get("{$this->baseUrl()}/getFile", ['file_id' => $fileId]);
        if (! $response->successful()) {
            return null;
        }

        $filePath = $response->json('result.file_path');
        if (! $filePath) {
            return null;
        }

        $base = rtrim(config('telegram.api_base_url', 'https://api.telegram.org'), '/');
        $fileUrl = "{$base}/file/bot{$this->botToken()}/{$filePath}";

        $fileResponse = Http::get($fileUrl);
        if (! $fileResponse->successful()) {
            return null;
        }

        return [
            'data' => $fileResponse->body(),
            'mime' => str_ends_with($filePath, '.mp3') ? 'audio/mp3' : 'audio/ogg',
        ];
    }

    /**
     * Transcribe speech audio to text using Gemini or Groq Whisper.
     */
    public function transcribeAudio(string $binaryData, string $mimeType = 'audio/ogg'): ?string
    {
        $geminiKey = config('telegram.gemini_api_key');
        if (! empty($geminiKey)) {
            $configuredModel = config('telegram.gemini_model', 'gemini-3.5-flash-lite');
            $candidateModels = array_unique([
                $configuredModel,
                'gemini-3.5-flash-lite',
                'gemini-3.5-flash',
                'gemini-3.6-flash',
                'gemini-flash-latest',
            ]);

            foreach ($candidateModels as $model) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}";

                try {
                    $response = Http::timeout(25)->post($url, [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'inlineData' => [
                                            'mimeType' => $mimeType,
                                            'data' => base64_encode($binaryData),
                                        ],
                                    ],
                                    [
                                        'text' => 'Tuliskan hanya transkrip teks persis dari audio rekaman suara ini dalam bahasa Indonesia tanpa tanda kutip. Audio ini berisi catatan pengeluaran/pemasukan keuangan keluarga sehari-hari (contoh kosakata yang sering muncul: beli kopi, makan siang, beli bensin, beli oli, bayar wifi, token listrik, pulsa, belanja dapur, dsb). Pastikan kata-kata belanja seperti "kopi" tidak keliru ditulis menjadi "koper".',
                                    ],
                                ],
                            ],
                        ],
                    ]);

                    if ($response->successful()) {
                        $text = trim((string) $response->json('candidates.0.content.parts.0.text', ''));

                        if ($text !== '') {
                            return $text;
                        }
                    }

                    Log::warning("TelegramBotService: Gemini transcription failed with model {$model}", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning("TelegramBotService: Gemini transcription exception with model {$model}: {$e->getMessage()}");
                }
            }
        }

        $groqKey = config('telegram.groq_api_key');
        if (! empty($groqKey)) {
            $response = Http::timeout(25)->withToken($groqKey)
                ->attach('file', $binaryData, 'audio.oga')
                ->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                    'model' => 'whisper-large-v3',
                    'language' => 'id',
                ]);

            if ($response->successful()) {
                $text = trim((string) $response->json('text', ''));

                return $text !== '' ? $text : null;
            }
        }

        return null;
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
     * Get current webhook status from Telegram.
     */
    public function getWebhookInfo(): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'description' => 'Bot token not configured'];
        }

        $response = Http::get("{$this->baseUrl()}/getWebhookInfo");

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
        // Handle inline keyboard button clicks
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);

            return;
        }

        $message = $update['message'] ?? null;
        if (! $message) {
            return;
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        if ($chatId === '') {
            return;
        }

        // Handle voice or audio messages
        if (isset($message['voice']) || isset($message['audio'])) {
            $voiceData = $message['voice'] ?? $message['audio'];
            $this->handleVoiceMessage($chatId, $voiceData);

            return;
        }

        $text = trim((string) ($message['text'] ?? ''));
        if ($text === '') {
            return;
        }

        // Handle button shortcuts or slash commands
        $lower = strtolower($text);
        if (str_starts_with($text, '/')
            || in_array($lower, ['💰 cek saldo', 'cek saldo', 'saldo', '📊 rekap bulan ini', 'rekap bulan ini', 'rekap', '📅 rekap hari ini', 'hari ini', 'rekap harian', 'harian', '📆 rekap minggu ini', 'minggu ini', 'rekap mingguan', 'mingguan', '📋 riwayat', 'riwayat', 'riwayat transaksi', '🗑️ batalkan transaksi', 'batalkan transaksi', 'batal', '❓ bantuan', 'bantuan', 'panduan'])) {
            $this->handleCommand($chatId, $text);

            return;
        }

        // Handle Natural Language Transaction
        $this->handleTransactionText($chatId, $text);
    }

    protected function handleVoiceMessage(string $chatId, array $voice): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        if (! $user) {
            $this->promptUnlinked($chatId);

            return;
        }

        $fileId = $voice['file_id'] ?? null;
        if (! $fileId) {
            return;
        }

        if (! $this->hasSpeechRecognition()) {
            $this->sendMessage(
                $chatId,
                "🎙️ *Pesan suara diterima!*\n\nSaat ini cara tercepat mencatat lewat suara adalah menggunakan **tombol mic pada keyboard HP**:\n1. Ketuk kolom chat di bawah\n2. Tekan ikon mikrofon 🎙️ pada keyboard HP Anda\n3. Ucapkan transaksi (misal: `\"makan siang 35.000\"`)\n4. Tekan Kirim!\n\n💡 *Ingin transkripsi Voice Note langsung?*\nTambahkan `GEMINI_API_KEY` (gratis dari Google AI Studio) di file `.env` untuk mengaktifkan AI Voice Transcribe otomatis!"
            );

            return;
        }

        $downloaded = $this->downloadTelegramFile($fileId);
        if (! $downloaded) {
            $this->sendMessage($chatId, '⚠️ Gagal mengunduh rekaman suara dari Telegram. Silakan coba kirim ulang.');

            return;
        }

        $transcript = $this->transcribeAudio($downloaded['data'], $downloaded['mime']);
        if (! $transcript) {
            $this->sendMessage($chatId, '⚠️ Suara tidak terdeteksi dengan jelas. Silakan coba rekam ulang lebih dekat ke mikrofon.');

            return;
        }

        $this->handleTransactionText($chatId, $transcript, isVoice: true);
    }

    protected function handleCommand(string $chatId, string $text): void
    {
        $cleanText = strtolower(trim($text));

        if (str_contains($cleanText, 'saldo')) {
            $this->handleSaldoCommand($chatId);

            return;
        }

        if (str_contains($cleanText, 'hari ini') || str_contains($cleanText, 'hariini') || str_contains($cleanText, 'harian') || $cleanText === '/hariini' || $cleanText === '/harian') {
            $this->handleRekapHarianCommand($chatId);

            return;
        }

        if (str_contains($cleanText, 'minggu ini') || str_contains($cleanText, 'mingguini') || str_contains($cleanText, 'mingguan') || $cleanText === '/mingguini' || $cleanText === '/mingguan') {
            $this->handleRekapMingguanCommand($chatId);

            return;
        }

        if (str_contains($cleanText, 'rekap') || str_contains($cleanText, 'laporan') || str_contains($cleanText, 'bulanini')) {
            $this->handleRekapCommand($chatId);

            return;
        }

        if (str_contains($cleanText, 'riwayat')) {
            $this->handleRiwayatCommand($chatId);

            return;
        }

        if (str_starts_with($cleanText, '/batal') || str_starts_with($cleanText, '/hapus') || str_starts_with($cleanText, '/undo') || in_array($cleanText, ['🗑️ batalkan transaksi', 'batalkan transaksi', 'batal', 'undo'])) {
            $parts = preg_split('/\s+/', trim($text), 2);
            $param = trim($parts[1] ?? '');
            $trxId = is_numeric($param) ? (int) $param : null;
            $this->handleBatalCommand($chatId, $trxId);

            return;
        }

        if (str_starts_with($cleanText, '/edit') || str_starts_with($cleanText, '/ubah')) {
            $parts = preg_split('/\s+/', trim($text), 2);
            $param = trim($parts[1] ?? '');
            $this->handleEditCommand($chatId, $param);

            return;
        }

        if (str_contains($cleanText, 'bantuan') || str_contains($cleanText, 'help') || str_contains($cleanText, 'panduan')) {
            $this->handleHelpCommand($chatId);

            return;
        }

        $parts = preg_split('/\s+/', trim($text), 2);
        $command = strtolower($parts[0] ?? '');
        $param = trim($parts[1] ?? '');

        if (str_contains($command, '@')) {
            $command = explode('@', $command)[0];
        }

        switch ($command) {
            case '/start':
                $this->handleStartCommand($chatId, $param);
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
                '⚠️ *Token tidak valid atau sudah kedaluwarsa.*\n\nSilakan buka aplikasi KeySaku di menu *Pengaturan > Akun Telegram* untuk mendapatkan tautan atau token terbaru.'
            );

            return;
        }

        $user = User::where('telegram_chat_id', $chatId)->first();
        if ($user) {
            $this->sendMessage(
                $chatId,
                "Halo, *{$user->name}*! Akun Telegram Anda sudah terhubung ke KeySaku.\n\nAnda bisa langsung mencatat transaksi (misal: `Makan siang 35rb`) atau gunakan tombol di bawah untuk cek saldo dan rekap bulanan."
            );

            return;
        }

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

    public function rekapPeriodKeyboard(string $activePeriod = 'monthly'): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => ($activePeriod === 'daily' ? '🔘 ' : '').'📅 Hari Ini', 'callback_data' => 'rekap:daily'],
                    ['text' => ($activePeriod === 'weekly' ? '🔘 ' : '').'📆 Minggu Ini', 'callback_data' => 'rekap:weekly'],
                    ['text' => ($activePeriod === 'monthly' ? '🔘 ' : '').'📊 Bulan Ini', 'callback_data' => 'rekap:monthly'],
                ],
            ],
        ];
    }

    public function getRekapHarianText(Household $household): string
    {
        $dateFormatted = now()->translatedFormat('l, d F Y');
        $today = now()->toDateString();

        $transactions = Transaction::where('household_id', $household->id)
            ->whereDate('transaction_date', $today)
            ->with('category')
            ->latest('id')
            ->get();

        $incomeTotal = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $expenseTotal = (float) $transactions->where('type', Transaction::TYPE_EXPENSE)->sum('amount');
        $surplus = $incomeTotal - $expenseTotal;

        $formattedIncome = number_format($incomeTotal, 0, ',', '.');
        $formattedExpense = number_format($expenseTotal, 0, ',', '.');
        $formattedSurplus = number_format(abs($surplus), 0, ',', '.');
        $surplusLabel = $surplus >= 0 ? 'Surplus Hari Ini' : 'Defisit Hari Ini';
        $surplusEmoji = $surplus >= 0 ? '🟢' : '🔴';

        if ($transactions->isEmpty()) {
            return "📅 *Rekap Harian — {$dateFormatted}*\n\nBelum ada transaksi yang dicatat untuk hari ini.\n\n💡 _Kirim chat atau pesan suara untuk mencatat transaksi hari ini!_";
        }

        $trxLines = [];
        foreach ($transactions as $t) {
            $typeEmoji = $t->type === Transaction::TYPE_INCOME ? '🟢' : '🔴';
            $amt = number_format((float) $t->amount, 0, ',', '.');
            $cat = $t->category?->name ?? 'Lainnya';
            $sourceIcon = $t->source === Transaction::SOURCE_VOICE ? '🎙️ ' : '';
            $trxLines[] = "• {$sourceIcon}*{$t->note}* — {$typeEmoji} Rp {$amt} ({$cat})";
        }

        $expenseTransactions = $transactions->where('type', Transaction::TYPE_EXPENSE);
        $byCategory = $expenseTransactions->groupBy('category_id')->map(function ($group) {
            return [
                'name' => $group->first()->category?->name ?? 'Lainnya',
                'icon' => $group->first()->category?->icon ?? '🏷️',
                'amount' => (float) $group->sum('amount'),
            ];
        })->sortByDesc('amount')->take(3);

        $categoryLines = [];
        $rank = 1;
        foreach ($byCategory as $cat) {
            $catAmount = $cat['amount'];
            $pct = $expenseTotal > 0 ? (int) round(($catAmount / $expenseTotal) * 100) : 0;
            $filledBars = (int) round(($pct / 100) * 8);
            $emptyBars = max(0, 8 - $filledBars);
            $bar = str_repeat('█', $filledBars).str_repeat('░', $emptyBars);
            $formattedCatAmount = number_format($catAmount, 0, ',', '.');

            $categoryLines[] = "{$rank}. {$cat['icon']} *{$cat['name']}*: Rp {$formattedCatAmount} ({$pct}%)\n   `[{$bar}]`";
            $rank++;
        }

        $detailSection = "\n\n📝 *Rincian Transaksi Hari Ini:*\n".implode("\n", $trxLines);
        $categorySection = ! empty($categoryLines) ? "\n\n🏷️ *Kategori Pengeluaran Hari Ini:*\n".implode("\n", $categoryLines) : '';

        return "📅 *Rekap Harian — {$dateFormatted}*\n\n📈 *Pemasukan:* Rp {$formattedIncome}\n📉 *Pengeluaran:* Rp {$formattedExpense} ({$transactions->count()} transaksi)\n━━━━━━━━━━━━━━━━━━━━\n{$surplusEmoji} *{$surplusLabel}:* Rp {$formattedSurplus}{$detailSection}{$categorySection}";
    }

    public function getRekapMingguanText(Household $household): string
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $rangeLabel = $startOfWeek->translatedFormat('d M').' - '.$endOfWeek->translatedFormat('d M Y');

        $transactions = Transaction::where('household_id', $household->id)
            ->whereBetween('transaction_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->with('category')
            ->latest('transaction_date')
            ->get();

        $incomeTotal = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $expenseTotal = (float) $transactions->where('type', Transaction::TYPE_EXPENSE)->sum('amount');
        $surplus = $incomeTotal - $expenseTotal;

        $formattedIncome = number_format($incomeTotal, 0, ',', '.');
        $formattedExpense = number_format($expenseTotal, 0, ',', '.');
        $formattedSurplus = number_format(abs($surplus), 0, ',', '.');
        $surplusLabel = $surplus >= 0 ? 'Surplus Pekan Ini' : 'Defisit Pekan Ini';
        $surplusEmoji = $surplus >= 0 ? '🟢' : '🔴';

        if ($transactions->isEmpty()) {
            return "📆 *Rekap Mingguan — {$rangeLabel}*\n\nBelum ada transaksi yang dicatat untuk pekan ini.\n\n💡 _Kirim chat atau pesan suara untuk mencatat transaksi!_";
        }

        $daysOfWeek = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $expenseTransactions = $transactions->where('type', Transaction::TYPE_EXPENSE);
        $dailyLines = [];
        $expensesByDay = $expenseTransactions->groupBy(function ($t) {
            return Carbon::parse($t->transaction_date)->dayOfWeekIso;
        });

        foreach ($daysOfWeek as $dayNum => $dayName) {
            $dayTrxs = $expensesByDay->get($dayNum, collect());
            if ($dayTrxs->isNotEmpty()) {
                $dayTotal = (float) $dayTrxs->sum('amount');
                $dailyLines[] = "• *{$dayName}:* Rp ".number_format($dayTotal, 0, ',', '.')." ({$dayTrxs->count()} transaksi)";
            }
        }

        $dailySection = ! empty($dailyLines) ? "\n\n📆 *Pengeluaran Harian Pekan Ini:*\n".implode("\n", $dailyLines) : '';

        $byCategory = $expenseTransactions->groupBy('category_id')->map(function ($group) {
            return [
                'name' => $group->first()->category?->name ?? 'Lainnya',
                'icon' => $group->first()->category?->icon ?? '🏷️',
                'amount' => (float) $group->sum('amount'),
            ];
        })->sortByDesc('amount')->take(3);

        $categoryLines = [];
        $rank = 1;
        foreach ($byCategory as $cat) {
            $catAmount = $cat['amount'];
            $pct = $expenseTotal > 0 ? (int) round(($catAmount / $expenseTotal) * 100) : 0;
            $filledBars = (int) round(($pct / 100) * 8);
            $emptyBars = max(0, 8 - $filledBars);
            $bar = str_repeat('█', $filledBars).str_repeat('░', $emptyBars);
            $formattedCatAmount = number_format($catAmount, 0, ',', '.');

            $categoryLines[] = "{$rank}. {$cat['icon']} *{$cat['name']}*: Rp {$formattedCatAmount} ({$pct}%)\n   `[{$bar}]`";
            $rank++;
        }

        $categorySection = ! empty($categoryLines) ? "\n\n🏷️ *Kategori Terbesar Pekan Ini:*\n".implode("\n", $categoryLines) : '';

        return "📆 *Rekap Mingguan — {$rangeLabel}*\n\n📈 *Pemasukan:* Rp {$formattedIncome}\n📉 *Pengeluaran:* Rp {$formattedExpense} ({$transactions->count()} transaksi)\n━━━━━━━━━━━━━━━━━━━━\n{$surplusEmoji} *{$surplusLabel}:* Rp {$formattedSurplus}{$dailySection}{$categorySection}";
    }

    public function getRekapBulananText(Household $household): string
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $monthName = now()->translatedFormat('F Y');

        $transactions = Transaction::where('household_id', $household->id)
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->with('category')
            ->get();

        $incomeTotal = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $expenseTotal = (float) $transactions->where('type', Transaction::TYPE_EXPENSE)->sum('amount');
        $surplus = $incomeTotal - $expenseTotal;

        $formattedIncome = number_format($incomeTotal, 0, ',', '.');
        $formattedExpense = number_format($expenseTotal, 0, ',', '.');
        $formattedSurplus = number_format(abs($surplus), 0, ',', '.');
        $surplusPrefix = $surplus >= 0 ? 'Surplus Bulan Ini' : 'Defisit Bulan Ini';
        $surplusEmoji = $surplus >= 0 ? '🟢' : '🔴';

        $expenseTransactions = $transactions->where('type', Transaction::TYPE_EXPENSE);
        $byCategory = $expenseTransactions->groupBy('category_id')->map(function ($group) {
            return [
                'name' => $group->first()->category?->name ?? 'Lainnya',
                'icon' => $group->first()->category?->icon ?? '🏷️',
                'amount' => (float) $group->sum('amount'),
            ];
        })->sortByDesc('amount')->take(3);

        $categoryLines = [];
        $rank = 1;
        foreach ($byCategory as $cat) {
            $catAmount = $cat['amount'];
            $pct = $expenseTotal > 0 ? (int) round(($catAmount / $expenseTotal) * 100) : 0;
            $filledBars = (int) round(($pct / 100) * 8);
            $emptyBars = max(0, 8 - $filledBars);
            $bar = str_repeat('█', $filledBars).str_repeat('░', $emptyBars);
            $formattedCatAmount = number_format($catAmount, 0, ',', '.');

            $categoryLines[] = "{$rank}. {$cat['icon']} *{$cat['name']}*: Rp {$formattedCatAmount} ({$pct}%)\n   `[{$bar}]`";
            $rank++;
        }

        $categorySection = '';
        if (! empty($categoryLines)) {
            $categorySection = "\n\n🏷️ *Pengeluaran Terbesar:*\n".implode("\n", $categoryLines);
        }

        return "📊 *Rekap Keuangan — {$monthName}*\n\n📈 *Pemasukan:* Rp {$formattedIncome}\n📉 *Pengeluaran:* Rp {$formattedExpense}\n━━━━━━━━━━━━━━━━━━━━\n{$surplusEmoji} *{$surplusPrefix}:* Rp {$formattedSurplus}{$categorySection}\n\n_Gunakan tombol di bawah untuk navigasi periode._";
    }

    protected function handleRekapCommand(string $chatId): void
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

        $this->sendMessage(
            $chatId,
            $this->getRekapBulananText($household),
            $this->rekapPeriodKeyboard('monthly')
        );
    }

    protected function handleRekapHarianCommand(string $chatId): void
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

        $this->sendMessage(
            $chatId,
            $this->getRekapHarianText($household),
            $this->rekapPeriodKeyboard('daily')
        );
    }

    protected function handleRekapMingguanCommand(string $chatId): void
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

        $this->sendMessage(
            $chatId,
            $this->getRekapMingguanText($household),
            $this->rekapPeriodKeyboard('weekly')
        );
    }

    protected function handleRiwayatCommand(string $chatId): void
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

        $transactions = Transaction::where('household_id', $household->id)
            ->latest('id')
            ->take(5)
            ->get();

        if ($transactions->isEmpty()) {
            $this->sendMessage($chatId, '📋 Belum ada transaksi yang tercatat di keluarga Anda.');

            return;
        }

        $lines = ["📋 *5 Transaksi Terakhir Keluarga Anda:*\n"];
        $inlineButtons = [];
        $numberEmojis = ['1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣'];

        foreach ($transactions->values() as $index => $trx) {
            $emoji = $numberEmojis[$index] ?? '•';
            $typeEmoji = $trx->type === Transaction::TYPE_INCOME ? '🟢' : '🔴';
            $formattedAmount = number_format((float) $trx->amount, 0, ',', '.');
            $categoryName = $trx->category?->name ?? 'Lainnya';
            $note = $trx->note ?: '-';

            $lines[] = "{$emoji} *#{$trx->id}* • *{$note}*\n     {$typeEmoji} Rp {$formattedAmount} ({$categoryName})";

            $inlineButtons[] = [
                ['text' => "🗑️ Hapus #{$trx->id} ({$note})", 'callback_data' => "del_trx:{$trx->id}"],
            ];
        }

        $lines[] = "\n💡 *Aksi via Chat:*";
        $lines[] = '• *Hapus:* Ketik `/hapus <ID>` (contoh: `/hapus 9`)';
        $lines[] = '• *Edit:* Ketik `/edit <ID> <catatan baru> <nominal>` (contoh: `/edit 9 beli kopi 37rb`)';
        $lines[] = '• Atau ketuk tombol di bawah untuk hapus langsung:';

        $this->sendMessage($chatId, implode("\n", $lines), [
            'inline_keyboard' => $inlineButtons,
        ]);
    }

    protected function handleEditCommand(string $chatId, string $payload): void
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

        if (! preg_match('/^(\d+)\s+(.+)$/s', trim($payload), $matches)) {
            $this->sendMessage(
                $chatId,
                "⚠️ *Format /edit belum tepat.*\n\n*Format:*\n`/edit <ID> <catatan baru> <nominal>`\n\n*Contoh:*\n• `/edit 9 beli kopi 37.000`\n• `/edit 9 kopi 37rb kategori makanan`\n\n💡 Ketik /riwayat untuk melihat daftar ID transaksi."
            );

            return;
        }

        $transactionId = (int) $matches[1];
        $newContent = trim($matches[2]);

        $transaction = Transaction::where('id', $transactionId)
            ->where('household_id', $household->id)
            ->first();

        if (! $transaction) {
            $this->sendMessage($chatId, "⚠️ Transaksi #{$transactionId} tidak ditemukan di keluarga Anda.\nKetik /riwayat untuk melihat ID transaksi.");

            return;
        }

        $categories = $household->categories()->get();
        $draft = $this->parser->parse($newContent, $categories);

        $newAmount = (float) ($draft['amount'] ?? 0);
        if ($newAmount > 0) {
            $transaction->amount = $newAmount;
        }

        $newNote = trim((string) ($draft['note'] ?? ''));
        if ($newNote !== '') {
            $transaction->note = $newNote;
        }

        if (! empty($draft['category_id'])) {
            $transaction->category_id = $draft['category_id'];
        }

        if (! empty($draft['type'])) {
            $transaction->type = $draft['type'];
        }

        if (! empty($draft['transaction_date'])) {
            $transaction->transaction_date = $draft['transaction_date'];
        }

        $transaction->save();

        $account = $transaction->account?->fresh();
        $formattedAmount = number_format((float) $transaction->amount, 0, ',', '.');
        $categoryName = $transaction->category?->name ?? 'Lainnya';
        $accountName = $account ? $account->name : 'Kas';
        $newBalance = $account ? 'Rp '.number_format((float) $account->balance, 0, ',', '.') : '-';

        $this->sendMessage(
            $chatId,
            "✏️ *Transaksi Berhasil Diperbarui!*\n\n• *ID:* #{$transaction->id}\n• *Catatan:* {$transaction->note}\n• *Nominal:* Rp {$formattedAmount}\n• *Kategori:* {$categoryName}\n• *Akun:* {$accountName}\n\n💰 *Sisa Saldo {$accountName}:* {$newBalance}"
        );
    }

    protected function handleBatalCommand(string $chatId, ?int $transactionId = null): void
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

        if ($transactionId !== null) {
            $transaction = Transaction::where('id', $transactionId)
                ->where('household_id', $household->id)
                ->first();

            if (! $transaction) {
                $this->sendMessage(
                    $chatId,
                    "⚠️ Transaksi #{$transactionId} tidak ditemukan atau bukan milik keluarga Anda.\nKetik /riwayat untuk melihat daftar ID transaksi."
                );

                return;
            }
        } else {
            // Look for the latest telegram or voice transaction within 60 minutes
            $transaction = Transaction::where('user_id', $user->id)
                ->whereIn('source', [Transaction::SOURCE_TELEGRAM, Transaction::SOURCE_VOICE])
                ->where('created_at', '>=', now()->subMinutes(60))
                ->latest('id')
                ->first();

            if (! $transaction) {
                $this->sendMessage(
                    $chatId,
                    "ℹ️ Tidak ada transaksi dari Telegram dalam 60 menit terakhir yang dapat dibatalkan.\n\nKetik /riwayat untuk melihat transaksi sebelumnya dan menghapusnya dengan `/hapus <ID>`."
                );

                return;
            }
        }

        $note = $transaction->note;
        $id = $transaction->id;
        $formattedAmount = number_format((float) $transaction->amount, 0, ',', '.');
        $typeLabel = $transaction->type === Transaction::TYPE_INCOME ? 'Pemasukan' : 'Pengeluaran';
        $account = $transaction->account;

        // Delete transaction (TransactionObserver will restore account balance)
        $transaction->delete();

        $balanceInfo = $account ? "\n💰 Saldo *{$account->name}* dipulihkan: Rp ".number_format((float) $account->fresh()->balance, 0, ',', '.') : '';

        $this->sendMessage(
            $chatId,
            "🗑️ *Transaksi Dibatalkan!*\n\n• *ID:* #{$id}\n• *Jenis:* {$typeLabel}\n• *Catatan:* {$note}\n• *Nominal:* Rp {$formattedAmount}{$balanceInfo}"
        );
    }

    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $queryId = (string) ($callbackQuery['id'] ?? '');
        $data = (string) ($callbackQuery['data'] ?? '');
        $chatId = (string) ($callbackQuery['message']['chat']['id'] ?? '');
        $messageId = (int) ($callbackQuery['message']['message_id'] ?? 0);

        if ($data === 'rekap:daily') {
            $user = User::where('telegram_chat_id', $chatId)->first();
            if ($user && $user->household) {
                $this->answerCallbackQuery($queryId);
                if ($messageId > 0) {
                    $this->editMessageText(
                        $chatId,
                        $messageId,
                        $this->getRekapHarianText($user->household),
                        $this->rekapPeriodKeyboard('daily')
                    );
                }
            }

            return;
        }

        if ($data === 'rekap:weekly') {
            $user = User::where('telegram_chat_id', $chatId)->first();
            if ($user && $user->household) {
                $this->answerCallbackQuery($queryId);
                if ($messageId > 0) {
                    $this->editMessageText(
                        $chatId,
                        $messageId,
                        $this->getRekapMingguanText($user->household),
                        $this->rekapPeriodKeyboard('weekly')
                    );
                }
            }

            return;
        }

        if ($data === 'rekap:monthly') {
            $user = User::where('telegram_chat_id', $chatId)->first();
            if ($user && $user->household) {
                $this->answerCallbackQuery($queryId);
                if ($messageId > 0) {
                    $this->editMessageText(
                        $chatId,
                        $messageId,
                        $this->getRekapBulananText($user->household),
                        $this->rekapPeriodKeyboard('monthly')
                    );
                }
            }

            return;
        }

        if (str_starts_with($data, 'del_trx:')) {
            $transactionId = (int) str_replace('del_trx:', '', $data);
            $user = User::where('telegram_chat_id', $chatId)->first();

            if (! $user || ! $user->household) {
                $this->answerCallbackQuery($queryId, 'Akun belum terhubung ke KeySaku.', true);

                return;
            }

            $transaction = Transaction::where('id', $transactionId)
                ->where('household_id', $user->household->id)
                ->first();

            if (! $transaction) {
                $this->answerCallbackQuery($queryId, 'Transaksi tidak ditemukan atau sudah dihapus.', true);

                return;
            }

            $note = $transaction->note;
            $formattedAmount = number_format((float) $transaction->amount, 0, ',', '.');
            $account = $transaction->account;

            $transaction->delete();

            $balanceInfo = $account ? 'Rp '.number_format((float) $account->fresh()->balance, 0, ',', '.') : '-';

            $this->answerCallbackQuery($queryId, "Transaksi '{$note}' berhasil dihapus!");

            if ($messageId > 0) {
                $this->editMessageText(
                    $chatId,
                    $messageId,
                    "🗑️ *Transaksi Dibatalkan!*\n\n• *ID:* #{$transactionId}\n• *Catatan:* {$note}\n• *Nominal:* Rp {$formattedAmount}\n\n💰 Saldo dipulihkan: {$balanceInfo}"
                );
            }
        }
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
            '🔌 *Koneksi Diputus*\n\nAkun Telegram Anda telah diputuskan dari KeySaku. Anda tidak akan menerima notifikasi atau mencatat via bot ini lagi sampai dihubungkan kembali.'
        );
    }

    protected function handleHelpCommand(string $chatId): void
    {
        $this->sendMessage(
            $chatId,
            "📖 *Panduan Penggunaan Bot KeySaku*\n\nAnda bisa langsung mencatat pengeluaran atau pemasukan secepat mengirim chat atau pesan suara Voice Note!\n\n*Contoh Format:*\n• `makan siang 35000`\n• `kopi 25rb kategori hiburan`\n• `beli bensin 50.000 kemarin`\n• `gaji bulanan 8.000.000`\n\n*Perintah Tersedia:*\n• 💰 *Cek Saldo* — Cek saldo seluruh dompet & rekening\n• 📊 *Rekap Bulan Ini* — Ringkasan keuangan bulan berjalan\n• 📋 */riwayat* — Lihat 5 transaksi terakhir & tombol hapus cepat\n• 🗑️ */hapus <ID>* — Hapus transaksi tertentu (misal: `/hapus 9`)\n• ✏️ */edit <ID> <catatan> <nominal>* — Ubah transaksi (misal: `/edit 9 beli kopi 37rb`)\n• 🗑️ */batal* — Batalkan transaksi paling terakhir\n• /putus — Putuskan hubungan akun dari bot"
        );
    }

    protected function handleTransactionText(string $chatId, string $text, bool $isVoice = false): void
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
            'source' => $isVoice ? Transaction::SOURCE_VOICE : Transaction::SOURCE_TELEGRAM,
            'raw_voice_text' => $text,
        ]);

        $formattedAmount = number_format($amount, 0, ',', '.');
        $typeLabel = $type === Transaction::TYPE_INCOME ? 'Pemasukan' : 'Pengeluaran';
        $accountName = $account ? $account->name : 'Kas';
        $newBalance = $account ? 'Rp '.number_format((float) $account->fresh()->balance, 0, ',', '.') : '-';
        $dateFormatted = $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : date('d/m/Y');
        $voicePrefix = $isVoice ? "🎙️ *Suara Terdeteksi:* \"_{$text}_\"\n\n" : '';

        $inlineMarkup = [
            'inline_keyboard' => [
                [
                    ['text' => '🗑️ Batalkan Transaksi Ini', 'callback_data' => "del_trx:{$transaction->id}"],
                ],
            ],
        ];

        $this->sendMessage(
            $chatId,
            "{$voicePrefix}✅ *{$typeLabel} Berhasil Dicatat!*\n\n• *ID Transaksi:* #{$transaction->id}\n• *Catatan:* {$note}\n• *Nominal:* Rp {$formattedAmount}\n• *Kategori:* {$category->name}\n• *Akun:* {$accountName}\n• *Tanggal:* {$dateFormatted}\n\n💰 *Sisa Saldo {$accountName}:* {$newBalance}\n\n_(Salah catat? Klik tombol di bawah atau ketik /hapus {$transaction->id})_",
            $inlineMarkup
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
