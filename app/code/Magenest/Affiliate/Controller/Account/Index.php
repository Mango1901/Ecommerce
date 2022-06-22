<?php


namespace Magenest\Affiliate\Controller\Account;

use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Account;

/**
 * Class Index
 * @package Magenest\Affiliate\Controller\Account
 */
class Index extends Account
{
    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Kcoin Wallet'));

        return $resultPage;
    }
}
