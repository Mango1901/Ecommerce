<?php

namespace Magenest\PaymentEPay\Api\Data;

use Magenest\Affiliate\Model\Account;
use Magenest\Affiliate\Model\Withdraw;

/**
 * Handle Disbursement Interface
 *
 * @api
 * @since 100.0.2
 */
interface HandleDisbursementInterface
{
    /**
     * @param Withdraw $withdraw
     * @return mixed
     */
    public function execute($withdraw);
}
