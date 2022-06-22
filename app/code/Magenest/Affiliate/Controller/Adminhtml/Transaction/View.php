<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Transaction;

use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class View
 * @package Magenest\Affiliate\Controller\Adminhtml\Transaction
 */
class View extends Transaction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|Page
     */
    public function execute()
    {
        $transactionId = (int)$this->getRequest()->getParam('id');
        /** @var \Magenest\Affiliate\Model\Transaction $transaction */
        $transaction = $this->_transactionFactory->create();
        if ($transactionId) {
            $transaction->load($transactionId);
        }
        $this->_coreRegistry->register('current_transaction', $transaction);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::transaction');
        $resultPage->getConfig()->getTitle()->set(__('Transactions'));

        $title = $transaction->getId() ? __('View Transaction "%1"', $transaction->getId()) : __('New Transaction');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
