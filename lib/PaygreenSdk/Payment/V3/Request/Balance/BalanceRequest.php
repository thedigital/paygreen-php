<?php

namespace Paygreen\Sdk\Payment\V3\Request\Balance;

use GuzzleHttp\Psr7\Request;
use Paygreen\Sdk\Core\Encoder\JsonEncoder;
use Paygreen\Sdk\Core\Normalizer\CleanEmptyValueNormalizer;
use Paygreen\Sdk\Core\Serializer\Serializer;
use Psr\Http\Message\RequestInterface;

class BalanceRequest extends \Paygreen\Sdk\Core\Request\Request
{
    /**
     * @param string $shopId
     *
     * @return Request|RequestInterface
     */
    public function getGetRequest($shopId = null)
    {
        if ($shopId === null) {
            $shopId = $this->environment->getShopId();
        }

        $body = [
            'shop_id' => $shopId,
        ];

        return $this->requestFactory->create(
            '/balance/?' . http_build_query($body),
            null,
            'GET'
        )->withAuthorization()->isJson()->getRequest();
    }
}