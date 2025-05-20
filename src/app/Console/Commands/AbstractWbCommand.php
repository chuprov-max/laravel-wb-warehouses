<?php

namespace App\Console\Commands;

use Dakword\WBSeller\APIToken;
use Illuminate\Console\Command;
use Dakword\WBSeller\API;

abstract class AbstractWbCommand extends Command
{
    /**
     * @var API
     */
    protected $wbSellerApi;

    abstract public function customHandle();

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = [
            'masterkey' => config('services.wildberries.apiKey'),
        ];
        $this->wbSellerApi = new API($options);

        $this->customHandle();
    }
}
