<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\AdvancedReports\Controller\Adminhtml;

abstract class Paymenttype extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Init action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_AdvancedReports::reports');
        $resultPage->getConfig()->getTitle()->prepend(__('Sales by Payment Type'));
        $resultPage->addBreadcrumb(__('Advanced Reports'), __('Advanced Reports'));

        /** @var \Aheadworks\AdvancedReports\Block\View $viewContainer */
        $viewContainer = $resultPage->getLayout()->getBlock('aw_arep.view_container');
        $viewContainer->setCurrentMenu(\Aheadworks\AdvancedReports\Block\View\Menu::ITEM_SALES_PAYMENTTYPE);
        $viewContainer->getChildBlock('breadcrumbs')->setTitle(__('Sales by Payment Type'));
        return $resultPage;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aheadworks_AdvancedReports::reports_paymenttype');
    }
}
