<?php


namespace Magenest\Affiliate\Controller\Account;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Account;

/**
 * Class Banner
 * @package Magenest\Affiliate\Controller\Account
 */
class Banner extends Account
{
    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if (!$this->dataHelper->getConfigGeneral('enable_banner')) {
            return $this->resultRedirectFactory->create()->setPath('customer/account');
        }
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
