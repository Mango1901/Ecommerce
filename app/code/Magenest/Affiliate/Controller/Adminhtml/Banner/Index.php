<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Banner;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Banner;

/**
 * Class Index
 * @package Magenest\Affiliate\Controller\Adminhtml\Banner
 */
class Index extends Banner
{
    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::banner');
        $resultPage->getConfig()->getTitle()->prepend((__('Banners')));

        return $resultPage;
    }
}
