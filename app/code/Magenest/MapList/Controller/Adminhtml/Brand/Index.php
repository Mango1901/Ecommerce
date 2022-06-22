<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 09:15
 */

namespace Magenest\MapList\Controller\Adminhtml\Brand;

use Magenest\MapList\Controller\Adminhtml\Brand;

/**
 * Class Index
 * @package Magenest\MapList\Controller\Adminhtml\Brand
 */
class Index extends Brand
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_MapList::list_brand');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Brand'));
        $resultPage->addBreadcrumb(__('Brand'), __('Brand'));
        $resultPage->addBreadcrumb(__('Magenest Brand'), __('Magenest Brand'));

        return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_MapList::list_brand');
    }
}
