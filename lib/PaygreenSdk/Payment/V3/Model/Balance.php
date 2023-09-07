<?php

namespace Paygreen\Sdk\Payment\V3\Model;

class Balance
{
    /**
     * @var int|null
     */
    private $balance = null;

    /**
     * @var string|null
     */
    private $paymentConfig = null;

    /**
     * @return int|null
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param int|null $amount
     * @return Balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentConfig()
    {
        return $this->paymentConfig;
    }

    /**
     * @param string|null $payment_config
     * @return Balance
     */
    public function setPaymentConfig($paymentConfig)
    {
        $this->paymentConfig = $paymentConfig;
        return $this;
    }
}