<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Index;

/**
 * Class TransactionHistory
 * @package Magenest\RewardPoints\Controller\Adminhtml\Index
 */
class TransactionHistory extends \Magento\Customer\Controller\Adminhtml\Index
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $customerId   = $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        $block        = $resultLayout->getLayout()->getBlock('admin.transaction.history');
        $block->setCustomerId($customerId)->setUseAjax(true);

        return $resultLayout;
    }
}
