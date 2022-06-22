<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Transaction;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magenest\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class Create
 * @package Magenest\Affiliate\Controller\Adminhtml\Transaction
 */
class Create extends Transaction
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('view');
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Affiliate::add_transaction');
    }
}
