<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Campaign;

use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Campaign;

/**
 * Class Index
 * @package Magenest\Affiliate\Controller\Adminhtml\Campaign
 */
class Index extends Campaign
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::campaign');
        $resultPage->getConfig()->getTitle()->prepend((__('Campaigns')));

        return $resultPage;
    }
}
