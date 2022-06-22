<?php


namespace Magenest\Affiliate\Controller\Account;

use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Account;

/**
 * Class Signup
 * @package Magenest\Affiliate\Controller\Account
 */
class Signup extends Account
{
    /**
     * @return Redirect|Page
     */
    public function execute()
    {
        $account = $this->dataHelper->getCurrentAffiliate();
        if ($account && $account->getId()) {
            if (!$account->isActive()) {
                $this->messageManager->addNoticeMessage(__('Your account is not active. Please contact us.'));
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*');

            return $resultRedirect;
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setHeader('Login-Required', 'true');

        return $resultPage;
    }
}
