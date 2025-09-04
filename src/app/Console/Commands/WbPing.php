<?php

namespace App\Console\Commands;

use App\Services\SuppliesApiClient;
use Dakword\WBSeller\APIToken;
use Illuminate\Console\Command;

class WbPing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check connection to Wildberries API using Common()->ping()';

    /**
     * Execute the console command.
     */
    public function handle(SuppliesApiClient $suppliesApiClient): int
    {
        try {
            $custom = $suppliesApiClient->getCommon();
            $response = $custom->ping();

            if ($response) {
                $this->info('✅ Подключение успешно! Пинг выполнен.');
                $token = new APIToken(config('services.wildberries.apiKey'));
                echo "\n".$token->expireDate()->format('Y-m-d H:i:s'); // 2024-09-20 16:21:04
                echo "\n".$token->isExpired() ? 'Просроченный' : 'Действительный';
                echo "\n".$token->isReadOnly() ? 'Только чтение' : 'Чтение и запись';
                echo "\n".$token->isTest() ? 'Для тестовой среды' : 'Рабочий';
                echo "\n".$token->sellerId(); // 284034
                echo "\n".$token->sellerUUID(); // 123e4567-e89b-12d3-a456-426655440000
                echo "\n".$token->accessTo('marketplace') ? 'Yes' : 'No'; // Yes
                echo "\n".$token->accessTo('common') ? 'Yes' : 'No'; // Yes
                echo "\n".$token->accessTo('chat') ? 'Yes' : 'No'; // No
                echo "\n".implode(',', $token->accessList()); // 'Цены и скидки, Маркетплейс, Документы'
                echo "\n".implode(',', array_keys($token->accessList())); // '3, 4, 12' - Позиции бита
                echo "\n".$token."\n";
            } else {
                $this->error('❌ Пинг не выполнен. Проверьте API-ключ.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }
}
