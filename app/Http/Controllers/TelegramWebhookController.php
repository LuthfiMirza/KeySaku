<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    /**
     * Handle incoming webhook requests from Telegram.
     */
    public function handle(Request $request, TelegramBotService $botService): JsonResponse
    {
        $secret = config('telegram.webhook_secret');
        if (! empty($secret)) {
            $headerSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if ($headerSecret !== $secret) {
                return response()->json(['ok' => false, 'error' => 'Unauthorized'], 401);
            }
        }

        $update = $request->all();
        if (! empty($update)) {
            $botService->handleUpdate($update);
        }

        return response()->json(['ok' => true]);
    }
}
