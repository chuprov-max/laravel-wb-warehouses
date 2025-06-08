<?php

namespace App\Services;

use Dakword\WBSeller\API;

class MarketplaceApiClient
{
    protected $wbSellerApi;

    public function __construct()
    {
        $options = [
            'masterkey' => config('services.wildberries.apiKey'),
        ];
        $this->wbSellerApi = new API($options);
    }

    public function getMarketplace(): API\Endpoint\Marketplace
    {
        return $this->wbSellerApi->Marketplace();
    }
}
