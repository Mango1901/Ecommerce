<?php

namespace Magenest\StoreCredit\Model\Action\Affiliate;

use Magenest\StoreCredit\Model\Action\Affiliate;

class Admin extends Affiliate
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Transaction Store Credit By Admin For Affiliate');
    }

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        return __('Affiliate Admin');
    }
}
