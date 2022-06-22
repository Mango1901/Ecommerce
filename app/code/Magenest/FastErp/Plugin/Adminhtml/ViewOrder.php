<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 * Created by PhpStorm.
 * User: crist
 * Date: 02/12/2021
 * Time: 16:13
 */

namespace Magenest\FastErp\Plugin\Adminhtml;

use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\Order;

class ViewOrder
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var Registry */
    private $registry;

    /**
     * @param Registry $registry
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Registry            $registry,
        SerializerInterface $serializer
    ) {
        $this->registry   = $registry;
        $this->serializer = $serializer;
    }

    /**
     * Before set layout
     *
     * @param View $subject
     * @param LayoutInterface $layout
     *
     * @return array
     */
    public function beforeSetLayout(View $subject, LayoutInterface $layout)
    {
        $order = $this->getOrder();
        $this->handleConfirmButton($subject, $order);
        $this->handleResyncButton($subject, $order);
        $this->handleCompleteButton($subject, $order);

        return [$layout];
    }

    /**
     * Retrieve order model object
     * @return Order
     */
    public function getOrder()
    {
        return $this->registry->registry('sales_order');
    }

    /**
     * @param View $subject
     * @param Order $order
     */
    protected function handleConfirmButton(View $subject, Order $order)
    {
        if ($this->checkConfirmable($order)) {
            $onclickJs = 'jQuery(\'#order_confirm_btn\').orderResyncDialog({message: \''
                . $this->getConfirmMessage() . '\', url: \'' . $subject->getUrl(
                    'fasterp/order/confirm',
                    ['order_id' => $order->getEntityId()]
                )
                . '\', title: \'Confirm Order'
                . '\'}).orderResyncDialog(\'showDialog\');';

            $subject->addButton(
                'order_confirm_btn',
                [
                    'label'          => __('Confirm'),
                    'class'          => 'edit primary',
                    'id'             => 'order_confirm_btn',
                    'onclick'        => $onclickJs,
                    'data_attribute' => [
                        'mage-init' => '{"orderResyncDialog":{}}',
                    ]
                ]
            );
        }
    }

    /**
     * @param View $subject
     * @param Order $order
     */
    protected function handleResyncButton(View $subject, Order $order)
    {
        if ($this->checkResyncable($order)) {
            $onclickJs = 'jQuery(\'#order_resync_erp_btn\').orderResyncDialog({message: \''
                . $this->getSyncMessage() . '\', url: \'' . $subject->getUrl(
                    'fasterp/sync/order',
                    ['order_id' => $order->getEntityId()]
                )
                . '\', title: \'Resync Order'
                . '\'}).orderResyncDialog(\'showDialog\');';

            $subject->addButton(
                'order_resync_erp_btn',
                [
                    'label'          => __('Resync ERP'),
                    'class'          => 'edit primary',
                    'id'             => 'order_resync_erp_btn',
                    'onclick'        => $onclickJs,
                    'data_attribute' => [
                        'mage-init' => '{"orderResyncDialog":{}}',
                    ]
                ]
            );

            $subject->removeButton('order_ship');
        }
    }

    /**
     * @param View $subject
     * @param Order $order
     *
     * @return void
     */
    protected function handleCompleteButton(View $subject, Order $order)
    {
        if ($this->checkComplete($order)) {
            $onclickJs = 'jQuery(\'#order_complete_btn\').orderResyncDialog({message: \''
                . $this->getCompleteMessage() . '\', url: \'' . $subject->getUrl(
                    'fasterp/order/complete',
                    ['order_id' => $order->getEntityId()]
                )
                . '\', title: \'Mark As Complete'
                . '\'}).orderResyncDialog(\'showDialog\');';

            $subject->addButton(
                'order_complete_btn',
                [
                    'label'          => __('Complete'),
                    'class'          => 'edit primary',
                    'id'             => 'order_complete_btn',
                    'onclick'        => $onclickJs,
                    'data_attribute' => [
                        'mage-init' => '{"orderResyncDialog":{}}',
                    ]
                ]
            );
        }
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    private function checkConfirmable(Order $order)
    {
        return $order->getStatus() == "pending" || $order->getStatus() == "pending_paid";
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    private function getConfirmMessage()
    {
        return __('This order will be confirmed and change to next status to process.');
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    private function checkResyncable(Order $order)
    {
        return !$order->getErpId() && $order->hasShipments();
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    private function getSyncMessage()
    {
        return __('Are you sure? This order will be re-synced to ERP system.');
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    private function checkComplete(Order $order)
    {
        return in_array($order->getStatus(), [UpdateOrderStatus::PACKED_STATUS, UpdateOrderStatus::PROCESSING_SHIPMENT_STATUS]);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    private function getCompleteMessage()
    {
        return __('This will change the order status to "Complete". Are you sure?');
    }
}
