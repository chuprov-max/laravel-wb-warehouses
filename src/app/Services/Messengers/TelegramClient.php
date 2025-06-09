<?php

namespace App\Services\Messengers;

use Illuminate\Support\Facades\Http;

class TelegramClient  implements MessengerInterface
{
    /** @var string */
    private $botToken;

    /** @var string */
    private $chatId;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
    }

    public function send(string $message): bool
    {
        $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'MarkdownV2',
        ]);

        return $response->successful();
    }
}
