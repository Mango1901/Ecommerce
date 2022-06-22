<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Group;

use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Group;

/**
 * Class Index
 * @package Magenest\Affiliate\Controller\Adminhtml\Group
 */
class Index extends Group
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::group');
        $resultPage->getConfig()->getTitle()->prepend((__('Group')));

        return $resultPage;
    }
}
