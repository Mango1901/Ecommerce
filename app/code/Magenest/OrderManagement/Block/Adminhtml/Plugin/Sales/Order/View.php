<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderManagement\Block\Adminhtml\Plugin\Sales\Order;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Registry;
use Magento\Framework\AuthorizationInterface;
use Magenest\OrderManagement\Model\Order;
use Magenest\OrderManagement\Controller\Adminhtml\Order\CompleteShipment;
use Magenest\OrderManagement\Helper\Authorization as AuthorizationHelper;

/**
 * Class View
 * @package Magenest\OrderManagement\Block\Adminhtml\Plugin\Sales\Order
 */
class View
{
    /**
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var AuthorizationHelper
     */
    protected $_authorizationHelper;

    /**
     * @var Order
     */
    protected $_omOrder;

    /**
     * @var \Magenest\OrderManagement\Model\Config\SourceOption
     */
    protected $sourceOption;

    /**
     * Constructor.
     *
     * @param Registry $registry
     * @param AuthorizationInterface $authorization
     * @param AuthorizationHelper $authorizationHelper
     * @param Order $omOrder
     * @param \Magenest\OrderManagement\Model\Config\SourceOption $sourceOption
     */
    function __construct(
        Registry $registry,
        AuthorizationInterface $authorization,
        AuthorizationHelper $authorizationHelper,
        Order $omOrder,
        \Magenest\OrderManagement\Model\Config\SourceOption $sourceOption
    ) {
        $this->sourceOption = $sourceOption;
        $this->_coreRegistry = $registry;
        $this->_authorization = $authorization;
        $this->_authorizationHelper = $authorizationHelper;
        $this->_omOrder = $omOrder;
    }

    /**
     * Before set layout
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\View $subject
     * @param LayoutInterface $layout
     * @return array
     */
    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $subject, LayoutInterface $layout)
    {
        $order = $this->getOrder();
        if ($this->_isAllowedAction('Magenest_OrderManagement::customer_service_wfs')
            && ($order->getStatus() == 'pending')
        ) {
            $subject->addButton('wait_supplier', [
                'label' => __('Wait for Supplier'),
                'class' => 'wait_supplier',
                'onclick' => 'setLocation(\'' . $subject->getUrl('salesom/order/waitsupplier') . '\')'
            ]);
        }

        if ($this->_isAllowedAction('Magenest_OrderManagement::supplier_action') && $order->getStatus() == Order::WAIT_SUPPLIER_CODE) {
            $onclickJs = 'jQuery(\'#confirm_deliver_goods\').om(\'showSupplierActionDialog\', \''
                . __('Your partner needs you to deliver goods, are you confirm?')
                . '\', \''
                . $subject->getUrl('salesom/order/supplieraction')
                . '\');';

            $subject->addButton('confirm_deliver_goods', [
                'label' => __('Confirm Deliver Goods'),
                'class' => 'confirm_deliver_goods',
                'onclick' => $onclickJs,
                'data_attribute' => [
                    'mage-init' => '{"Magenest_OrderManagement/js/om": {}}'
                ]
            ]);
        }

        if ($this->_isAllowedAction('Magenest_OrderManagement::warehouse_received_goods') && $order->getStatus() == Order::SUPPLIER_CONFIRMED_CODE) {
            $message = __('Are you confirmed to receive the goods?');
            $subject->addButton('confirm_received_goods', [
                'label' => __('Confirm Received Goods'),
                'class' => 'confirm_received_goods',
                'onclick' => "confirmSetLocation('{$message}', '{$subject->getUrl('salesom/order/receivedgoods')}')"
            ]);
        }

        if ($this->_isAllowedAction('Magenest_OrderManagement::customer_service_confirm') && $this->_omOrder->canConfirm($order)) {
            $onclickJs = 'jQuery(\'#confirm\').om(\'showConfirmDialog\', \''
                . __('Please confirm warehouse to process this order?')
                . '\', \''
                . $subject->getUrl('salesom/order/confirm')
                . '\', \''
                . \Zend_Json::encode($this->sourceOption->getOptionArray())
                . '\');';

            $subject->addButton('confirm', [
                'label' => __('Confirm Warehouse &amp; Sales'),
                'class' => 'confirm',
                'onclick' => $onclickJs,
                'data_attribute' => [
                    'mage-init' => '{"Magenest_OrderManagement/js/om": {}}'
                ]
            ]);
        }

        if ($this->_isAllowedAction('Magenest_OrderManagement::accounting_confirm') && $this->_omOrder->canConfirmPaid($order, false)) {
            $onclickJs = 'jQuery(\'#confirm_paid\').om(\'showDialog\', \''
                . __('Are you sure that order was paid?')
                . '\', \''
                . $subject->getUrl('salesom/order/confirmpaid')
                . '\', \''
                .  __("Yes, forward to warehouse")
                . '\');';

            $subject->addButton('confirm_paid', [
                'label' => __('Confirm Payment'),
                'class' => 'confirm_paid',
                'onclick' => $onclickJs,
                'data_attribute' => [
                    'mage-init' => '{"Magenest_OrderManagement/js/om": {}}'
                ]
            ], 0, 130);
            $subject->updateButton('order_invoice', 'sort_order', 120);
        }

        if ($this->_isAllowedAction('Magenest_OrderManagement::accounting_confirm_debt') && $this->_omOrder->canConfirmDebt($order)) {
            $subject->addButton('confirm_debt', [
                'label' => __('Confirm Debt'),
                'class' => 'confirm_debt',
                'onclick' => 'setLocation(\'' . $subject->getUrl('salesom/order/confirmDebt') . '\')'
            ]);
        }
        if ($this->_isAllowedAction('Magenest_OrderManagement::warehouse_received_returned_goods') && $order->getStatus() == Order::NEED_WAREHOUSE_CONFIRM_CODE) {
            $onclickJs = 'jQuery(\'#confirm_goods_returned\').om(\'showReturnedDialog\', \''
                . __('Are you confirmed goods returned?')
                . '\', \''
                . $subject->getUrl('salesom/order/returned')
                . '\');';

            $subject->addButton('confirm_goods_returned', [
                'label' => __('Confirm Goods Returned'),
                'class' => 'confirm_goods_returned',
                'onclick' => $onclickJs,
                'data_attribute' => [
                    'mage-init' => '{"Magenest_OrderManagement/js/om": {}}'
                ]
            ]);
        }

        if ($this->_isAllowedAction('Magenest_OrderManagement::accounting_confirm_reimbursed') && $order->getStatus() == Order::NEED_CONFIRM_REIMBURSEMENT_CODE) {
            $message = __('Are you confirmed?');
            $subject->addButton('confirm_reimbursed', [
                'label' => __('Confirm Reimbursed'),
                'class' => 'confirm_reimbursed',
                'onclick' => "confirmSetLocation('{$message}', '{$subject->getUrl('salesom/order/reimbursed')}')"
            ]);
        }

        if ($this->_allowConfirmShipment($order)) {
            $onclickJs = 'jQuery(\'#confirm_shipment_complete\').om(\'showDialog\', \''
                . __('Are you confirm that shipment is completed?')
                . '\', \''
                . $subject->getUrl('salesom/order/completeshipment')
                . '\');';

            $subject->addButton('confirm_shipment_complete', [
                'label' => __('Confirm Shipment Complete'),
                'class' => 'confirm_shipment_complete',
                'onclick' => $onclickJs,
                'data_attribute' => [
                    'mage-init' => '{"Magenest_OrderManagement/js/om": {}}'
                ]
            ], 0, 150);
        }

        if (in_array($order->getState(), ['new'])) {
            $subject->removeButton('order_hold');
            $subject->removeButton('order_unhold');
            $subject->removeButton('order_invoice');
            $subject->removeButton('order_ship');
        }

        return [$layout];
    }

    /**
     * Retrieve order model object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Allow confirm shipment
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    private function _allowConfirmShipment(\Magento\Sales\Model\Order $order)
    {
        $shipment = $order->getShipmentsCollection()->getItems();

        return $this->_isAllowedAction(CompleteShipment::ADMIN_RESOURCE) && $order->getStatus() == 'complete' && count($shipment) > 0;
    }
}
