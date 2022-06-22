<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Transaction;

use Magento\Backend\App\Action;
use Magenest\RewardPoints\Controller\Adminhtml\Transaction;

/**
 * Class Index
 * @package Magenest\RewardPoints\Controller\Adminhtml\Transaction
 */
class Index extends Transaction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Transaction History Manager'));
        return $resultPage;
    }
}
