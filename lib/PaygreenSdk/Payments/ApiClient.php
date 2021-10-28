<?php

namespace Paygreen\Sdk\Payments;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Paygreen\Sdk\Core\Components\Environment;
use Paygreen\Sdk\Core\HttpClient;
use Paygreen\Sdk\Payments\Exceptions\PaymentCreationException;
use Paygreen\Sdk\Payments\Interfaces\OrderInterface;

class ApiClient extends HttpClient
{
    const API_BASE_URL_SANDBOX = 'https://sandbox.paygreen.fr';
    const API_BASE_URL_PROD = 'https://paygreen.fr';

    public function __construct()
    {
        $environment = new Environment(
            getenv('PAYGREEN_PUBLIC_KEY'),
            getenv('PAYGREEN_PRIVATE_KEY'),
            getenv('PAYGREEN_API_SERVER')
        );

        parent::__construct($environment);

        $this->initClient();
    }

    /**
     * @param OrderInterface $order
     * @param int $amount
     * @param string $notifiedUrl
     * @param string $currency
     * @param string $paymentType
     * @param string $returnedUrl
     * @param array $metadata
     * @param array $eligibleAmount
     * @param string $ttl
     * @return Response
     * @throws PaymentCreationException
     */
    public function createCash(
        OrderInterface $order,
        $amount,
        $notifiedUrl,
        $paymentType = 'CB',
        $currency = 'EUR',
        $returnedUrl = '',
        $metadata = [],
        $eligibleAmount = [],
        $ttl = ''
    ) {
        try {
            $url = $this->parseUrlParameters(
                $this->getBaseUri() . '/api/{ui}/payins/transaction/cash',
                [
                    'ui' => $this->environment->getPublicKey()
                ]
            );

            $response = $this->client->post($url, [
                'json' => [
                    'orderId' => 'PG-' . $order->getReference(),
                    'amount' => $amount,
                    'currency' => $currency,
                    'paymentType' => $paymentType,
                    'notifiedUrl' => $notifiedUrl,
                    'returnedUrl' => $returnedUrl,
                    'buyer' => (object) [
                        'id' => $order->getCustomer()->getId(),
                        'lastName' => $order->getCustomer()->getLastName(),
                        'firstName' => $order->getCustomer()->getFirstName(),
                        'country' => $order->getCustomer()->getCountryCode()
                    ],
                    'shippingAddress' => (object) [
                        'lastName' => $order->getShippingAddress()->getLastName(),
                        'firstName' => $order->getShippingAddress()->getFirstName(),
                        'address' => $order->getShippingAddress()->getStreet(),
                        'zipCode' => $order->getShippingAddress()->getZipCode(),
                        'city' => $order->getShippingAddress()->getCity(),
                        'country' => $order->getShippingAddress()->getCountryCode()
                    ],
                    'billingAddress' => (object) [
                        'lastName' => $order->getBillingAddress()->getLastName(),
                        'firstName' => $order->getBillingAddress()->getFirstName(),
                        'address' => $order->getBillingAddress()->getStreet(),
                        'zipCode' => $order->getBillingAddress()->getZipCode(),
                        'city' => $order->getBillingAddress()->getCity(),
                        'country' => $order->getBillingAddress()->getCountryCode()
                    ],
                    'metadata' => $metadata,
                    'eligibleAmount' => $eligibleAmount,
                    'ttl' => $ttl
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->environment->getPrivateKey()
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            throw new PaymentCreationException(
                "An error occurred while creating a payment task for order '{$order->getReference()}'."
            );
        }
    }

    /**
     * @return string
     */
    private function getBaseUri()
    {
        if ($this->environment->getEnvironment() === 'SANDBOX') {
            $baseUri = self::API_BASE_URL_SANDBOX;
        } else {
            $baseUri = self::API_BASE_URL_PROD;
        }

        return $baseUri;
    }

    /**
     * @return void
     */
    private function initClient()
    {
        $this->client = new Client([
            'defaults' => [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => $this->buildUserAgentHeader()
                ]
            ]
        ]);
    }
}