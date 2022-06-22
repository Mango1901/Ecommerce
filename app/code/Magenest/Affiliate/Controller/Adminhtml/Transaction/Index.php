<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Transaction;

use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class Index
 * @package Magenest\Affiliate\Controller\Adminhtml\Transaction
 */
class Index extends Transaction
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $this->_eventManager->dispatch('mp_affiliate_transaction', ['result_page' => $resultPage]);
        $resultPage->setActiveMenu('Magenest_Affiliate::transaction');
        $resultPage->getConfig()->getTitle()->prepend((__('Transactions')));

        return $resultPage;
    }
}
