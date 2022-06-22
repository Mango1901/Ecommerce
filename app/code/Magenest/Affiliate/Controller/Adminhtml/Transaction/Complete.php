<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Transaction;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Magenest\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class Complete
 * @package Magenest\Affiliate\Controller\Adminhtml\Transaction
 */
class Complete extends Transaction
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $transactionId = $this->getRequest()->getParam('id');
        if ($transactionId) {
            $transaction = $this->_transactionFactory->create()->load($transactionId);
            if ($transaction->getId()) {
                try {
                    $transaction->complete();
                    $this->messageManager->addSuccess(__('The Transaction has been completed.'));
                } catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        }
        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }
}
