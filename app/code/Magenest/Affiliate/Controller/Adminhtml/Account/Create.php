<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Account;

use Magenest\Affiliate\Controller\Adminhtml\Account;

/**
 * Class Create
 * @package Magenest\Affiliate\Controller\Adminhtml\Account
 */
class Create extends Account
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
