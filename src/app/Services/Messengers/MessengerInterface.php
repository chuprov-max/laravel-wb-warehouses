<?php

namespace App\Services\Messengers;

interface MessengerInterface
{
    public function send(string $message): bool;
}
