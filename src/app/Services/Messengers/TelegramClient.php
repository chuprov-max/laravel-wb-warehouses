<?php

namespace App\Services\Messengers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramClient  implements MessengerInterface
{
    /** @var string */
    private $botToken;

    /** @var string */
    private $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.token');
        $this->chatId = config('services.telegram.chatId');
    }

    public function send(string $message): bool
    {
        $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'MarkdownV2',
        ]);

        if (!$response->successful()) {
            Log::channel('warehousesCoefficients')->info('Telegram response failed: ' , [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json(), // если ответ в JSON
            ]);
        }

        return $response->successful();
    }
}
