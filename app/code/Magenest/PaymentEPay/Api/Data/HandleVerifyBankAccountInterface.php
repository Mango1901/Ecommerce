<?php

namespace Magenest\PaymentEPay\Api\Data;

interface HandleVerifyBankAccountInterface
{
    /**
     * @param string $bankNo
     * @param string $accNo
     * @param string $accType
     * @param string $accountName
     * @return mixed
     */
    public function execute($bankNo, $accNo, $accType, $accountName);
}
