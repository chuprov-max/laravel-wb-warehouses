<?php

namespace App\Services;

use Dakword\WBSeller\API;

class SuppliesApiClient
{
    protected $wbSellerApi;

    public function __construct()
    {
        $options = [
            'masterkey' => config('services.wildberries.apiKey'),
        ];
        $this->wbSellerApi = new API($options);
    }

    /**
     * @return API\Endpoint\Supplies
     */
    public function getSupplies()
    {
        return $this->wbSellerApi->Supplies();
    }
}
