<?php

namespace App\Models;

use App\Helpers\WarehouseHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class SuitableCoefficient extends Model
{
    use HasFactory;

    const STATUS_CREATED = 1;
    const STATUS_NOTIFIED = 2;
    const STATUS_FAILED = 3;

    const BOX_TYPE_ID_KOROBA = 2;
    const BOX_TYPE_ID_MONOPALLETTY = 5;
    const BOX_TYPE_ID_SUPERSAFE = 6;

    protected $fillable = ['warehouse_id', 'coefficient', 'allow_unload', 'box_type_id', 'accept_date', 'status'];

    public function notify()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        $warehouseName = WarehouseHelper::getNameById($this->warehouse_id);
        $text = "<strong>Найден подходящий коэффициент для склада {$warehouseName} (id = {$this->warehouse_id})</strong><br/>
Коэффициент: {$this->coefficient}<br/>
Тип поставки: {$this->box_type_id}<br/>
Дата поставки: {$this->accept_date}<br/>
";

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML', // можно markdown
        ]);

        $this->status = self::STATUS_NOTIFIED;
        $this->saveQuietly();

        return $response->successful();
    }

}
