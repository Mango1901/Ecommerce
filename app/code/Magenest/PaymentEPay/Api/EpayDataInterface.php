<?php

namespace Magenest\PaymentEPay\Api;

interface EpayDataInterface
{

    /**
     * @param string $orderId
     * @param string $payType
     * @param string|null $payOption
     * @return string
     */
    public function prepareOrderData(string $orderId, string $payType, string $payOption = null);
}
