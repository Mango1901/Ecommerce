<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Plugin\Order;

use Magenest\OrderManagement\Model\Order;

/**
 * Class Index
 * @package Magenest\OrderManagement\Controller\Adminhtml\Plugin\Order
 */
class Index
{
    /**
     * After execute
     *
     * @param \Magento\Sales\Controller\Adminhtml\Order\Index $subject
     * @param $result
     * @return
     */
    public function afterExecute(\Magento\Sales\Controller\Adminhtml\Order\Index $subject, $result)
    {
        $result->getConfig()->getTitle()->prepend(__($this->_getPageTitle($subject)));

        return $result;
    }

    /**
     * Get page title
     *
     * @param \Magento\Sales\Controller\Adminhtml\Order\Index $subject
     * @return \Magento\Framework\Phrase|string|void
     */
    private function _getPageTitle(\Magento\Sales\Controller\Adminhtml\Order\Index $subject)
    {
        $type = $subject->getRequest()->getParam('type', false);
        $titleOptions = [
            Order::ACCOUNTING_STAFF => __('Accounting Orders'),
            Order::WAREHOUSE_STAFF => __('Warehouse Orders'),
            Order::CUSTOMER_SERVICE_STAFF => __('Customer Service Orders'),
            Order::SUPPLIER_STAFF => __('Supplier Orders')
        ];

        if($type && isset($titleOptions[$type])){
            return $titleOptions[$type];
        }

        return __('Orders');
    }
}