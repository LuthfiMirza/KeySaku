<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class TelegramWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:webhook 
                            {action=info : Action to perform: info, set, or delete}
                            {--url= : The webhook URL to set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Telegram Bot webhook (info, set, delete)';

    /**
     * Execute the console command.
     */
    public function handle(TelegramBotService $botService): int
    {
        if (! $botService->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN is not configured in .env file.');

            return self::FAILURE;
        }

        $action = strtolower($this->argument('action'));

        switch ($action) {
            case 'set':
                $url = $this->option('url') ?: route('telegram.webhook');
                $secret = config('telegram.webhook_secret');

                $this->info("Setting webhook to: {$url}");
                $result = $botService->setWebhook($url, $secret);

                if ($result['ok'] ?? false) {
                    $this->info('✅ Webhook successfully set!');
                    $this->line($result['description'] ?? 'OK');

                    return self::SUCCESS;
                }

                $this->error('Failed to set webhook: '.($result['description'] ?? 'Unknown error'));

                return self::FAILURE;

            case 'delete':
                $this->info('Deleting webhook...');
                $result = $botService->deleteWebhook();

                if ($result['ok'] ?? false) {
                    $this->info('✅ Webhook successfully deleted! You can now use telegram:poll.');

                    return self::SUCCESS;
                }

                $this->error('Failed to delete webhook: '.($result['description'] ?? 'Unknown error'));

                return self::FAILURE;

            case 'info':
            default:
                $this->info('Fetching webhook info from Telegram...');
                $info = $botService->getWebhookInfo();

                if (! ($info['ok'] ?? false)) {
                    $this->error('Failed to get webhook info: '.($info['description'] ?? 'Unknown error'));

                    return self::FAILURE;
                }

                $result = $info['result'] ?? [];
                $url = $result['url'] ?? '';

                $this->table(
                    ['Field', 'Value'],
                    [
                        ['URL', $url ?: '(None - Using Long Polling)'],
                        ['Pending Updates', $result['pending_update_count'] ?? 0],
                        ['Last Error Date', isset($result['last_error_date']) ? date('Y-m-d H:i:s', $result['last_error_date']) : '-'],
                        ['Last Error Message', $result['last_error_message'] ?? '-'],
                        ['Max Connections', $result['max_connections'] ?? '-'],
                    ]
                );

                return self::SUCCESS;
        }
    }
}
