<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Controller\Adminhtml\Rates;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Backend\App\Action;

/**
 * Class Index
 * @package Magenest\CustomTableRate\Controller\Adminhtml\Rates
 */
class Index extends Action implements HttpGetActionInterface
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = "Magenest_CustomTableRate::manage";

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Kangaroo TableRates Shipping'), __('Kangaroo TableRates Shipping'));
        $this->_addBreadcrumb(__('Kangaroo TableRates Shipping'), __('Kangaroo TableRates Shipping'));
        $this->_setActiveMenu(self::ADMIN_RESOURCE)->_addBreadcrumb(__('Manage Rates'), __('Manage Rates'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Shipping Rates'));
        $this->_view->renderLayout();
    }
}
