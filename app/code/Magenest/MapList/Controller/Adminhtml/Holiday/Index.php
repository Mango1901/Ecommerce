<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 09:15
 */

namespace Magenest\MapList\Controller\Adminhtml\Holiday;

use Magenest\MapList\Controller\Adminhtml\Holiday;

/**
 * Class Index
 * @package Magenest\MapList\Controller\Adminhtml\Holiday
 */
class Index extends Holiday
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_MapList::list_holiday');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Holiday'));
        $resultPage->addBreadcrumb(__('Holiday'), __('Holiday'));
        $resultPage->addBreadcrumb(__('Magenest Holiday'), __('Magenest Holiday'));

        return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_MapList::list_holiday');
    }
}
