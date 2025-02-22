<?php

namespace Paygreen\Sdk\Payment\V3\Request\PaymentConfig;

use Paygreen\Sdk\Core\Encoder\JsonEncoder;
use Paygreen\Sdk\Core\Normalizer\CleanEmptyValueNormalizer;
use Paygreen\Sdk\Core\Serializer\Serializer;
use Paygreen\Sdk\Payment\V3\Model\PaymentConfigInterface;
use Psr\Http\Message\RequestInterface;

class PaymentConfigRequest extends \Paygreen\Sdk\Core\Request\Request
{
    /**
     * @param $idPaymentConfig
     * @return RequestInterface
     */
    public function getGetRequest($idPaymentConfig)
    {
        return $this->requestFactory->create(
            '/payment/payment-configs/' . urlencode($idPaymentConfig),
            null,
            'GET'
        )->withAuthorization()->isJson()->getRequest();
    }

    /**
     * @param $filters
     * @param $pagination
     * @return RequestInterface
     */
    public function getListRequest($filters = [], $pagination = [])
    {
        return $this->requestFactory->create(
            '/payment/payment-configs?' . $this->getListParameters($filters, $pagination),
            null,
            'GET'
        )->withAuthorization()->isJson()->getRequest();
    }

    /**
     * @param PaymentConfigInterface $paymentConfig
     * @param string|null $shopId
     *
     * @return RequestInterface
     */
    public function getCreateRequest(PaymentConfigInterface $paymentConfig, $shopId = null)
    {
        if ($shopId === null) {
            $shopId = $this->environment->getShopId();
        }

        $body = [
            'shop_id' => $shopId,
            'currency' => $paymentConfig->getCurrency(),
            'platform' => $paymentConfig->getPlatform(),
            'selling_contract' => $paymentConfig->getSellingContractId(),
            'config' => $paymentConfig->getConfig()
        ];

        return $this->requestFactory->create(
            '/payment/payment-configs',
            (new Serializer([new CleanEmptyValueNormalizer()], [new JsonEncoder()]))->serialize($body, 'json')
        )->withAuthorization()->isJson()->getRequest();
    }
}