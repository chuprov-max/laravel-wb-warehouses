<?php

namespace App\Services;

use Dakword\WBSeller\API;
use Dakword\WBSeller\API\Endpoint\Common;

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

    public function getCommon():Common
    {
        return $this->wbSellerApi->Common();
    }
}
