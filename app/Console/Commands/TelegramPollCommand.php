<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class TelegramPollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:poll 
                            {--once : Run once and exit} 
                            {--timeout=30 : Long polling timeout in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and process updates from Telegram Bot (Auto-fetches messages queued while offline)';

    /**
     * Execute the console command.
     */
    public function handle(TelegramBotService $botService): int
    {
        if (! $botService->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN is not configured in .env file.');
            $this->line('Please add your bot token to .env: TELEGRAM_BOT_TOKEN=your_token_here');

            return self::FAILURE;
        }

        $botUsername = config('telegram.bot_username', 'Keysaku_bot');
        $this->info("🚀 Memulai Telegram Bot Polling untuk @{$botUsername}...");
        $this->line('Pesan yang dikirim saat server offline akan otomatis diproses.');
        $this->line('Tekan Ctrl+C untuk berhenti.');

        $offset = null;
        $timeout = (int) $this->option('timeout');
        $once = (bool) $this->option('once');

        do {
            try {
                $updates = $botService->getUpdates($offset, $timeout);

                foreach ($updates as $update) {
                    $updateId = $update['update_id'] ?? null;
                    if ($updateId !== null) {
                        $offset = $updateId + 1;
                    }

                    $botService->handleUpdate($update);

                    $sender = $update['message']['from']['first_name'] ?? 'Unknown';
                    $text = $update['message']['text'] ?? '[Media/Voice]';
                    $this->line("<info>[Update #{$updateId}]</info> Dari: {$sender} | Pesan: {$text}");
                }
            } catch (\Throwable $e) {
                $this->error("Error: {$e->getMessage()}");
                sleep(2);
            }

            if ($once) {
                break;
            }
        } while (true);

        return self::SUCCESS;
    }
}
