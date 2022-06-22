<?php

namespace Magenest\StoreCredit\Model\Action\Affiliate;

use Magenest\StoreCredit\Model\Action\Affiliate;

class WithdrawCreate extends Affiliate
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Transaction Store Credit By Withdraw Create For Affiliate');
    }

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        return __('Affiliate Withdraw Create');
    }
}
