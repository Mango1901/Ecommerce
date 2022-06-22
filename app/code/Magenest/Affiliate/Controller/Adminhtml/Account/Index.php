<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Account;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Account;

/**
 * Class Index
 * @package Magenest\Affiliate\Controller\Adminhtml\Account
 */
class Index extends Account
{
    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::account');
        $resultPage->getConfig()->getTitle()->prepend((__('Accounts')));

        return $resultPage;
    }
}
