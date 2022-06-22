<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Withdraw;

use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Withdraw;

/**
 * Class Index
 * @package Magenest\Affiliate\Controller\Adminhtml\Withdraw
 */
class Index extends Withdraw
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::withdraw');
        $resultPage->getConfig()->getTitle()->prepend((__('Withdraws')));

        return $resultPage;
    }
}
