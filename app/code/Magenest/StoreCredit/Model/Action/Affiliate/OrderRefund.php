<?php

namespace Magenest\StoreCredit\Model\Action\Affiliate;

use Magenest\StoreCredit\Model\Action\Affiliate;

class OrderRefund extends Affiliate
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Transaction Store Credit By Order Refund For Affiliate');
    }

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        return __('Affiliate Order Refund');
    }
}
