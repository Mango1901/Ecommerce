<?php

namespace Magenest\OrderCreditMemo\Plugin\Adminhtml;

use Magenest\PaymentEPay\Setup\Patch\Data\AddPendingPaidOrderStatus;
use Magenest\PaymentEPay\Setup\Patch\Data\UpdatePendingPaidOrderStatus;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\Order;
use Magento\Framework\View\LayoutInterface;

class ViewOrder
{
    const REFUND_OFFLINE = 0;
    const REFUND_ONLINE = 1;

    /** @var SerializerInterface */
    private $serializer;

    /** @var Registry */
    private $registry;

    /**
     * @param Registry $registry
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Registry $registry,
        SerializerInterface $serializer
    ) {
        $this->registry = $registry;
        $this->serializer = $serializer;
    }

    /**
     * Before set layout
     *
     * @param View $subject
     * @param LayoutInterface $layout
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSetLayout(View $subject, LayoutInterface $layout)
    {
        $order = $this->getOrder();
        if (
            $order->getPayment()->getMethodInstance()->isGateway() &&
            $order->canCreditmemo() &&
            $order->getStatus() === UpdatePendingPaidOrderStatus::STATUS_CODE
        ) {
            $this->handleCreditMemo($subject, $order);
        }

        return [$layout];
    }

    /**
     * Retrieve order model object
     *
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
    protected function handleCreditMemo(View $subject, Order $order)
    {
        $creditMemoOption = [
            [
                'label' => __('Online Refund'),
                'value' => self::REFUND_ONLINE,
            ],
            [
                'label' => __('Offline Refund'),
                'value' => self::REFUND_OFFLINE
            ]
        ];

        $onclickJs = 'jQuery(\'#order_credit_memo_option\').om(\'showConfirmCreditMemoDialog\', \''
            . __('Please confirm refund method to credit memo this order?')
            . '\', \''
            . $subject->getUrl('sales/order/creditMemoOption')
            . '\', \''
            . $this->serializer->serialize($creditMemoOption)
            . '\');';

        $subject->updateButton('order_creditmemo', 'id', 'order_credit_memo_option');
        $subject->updateButton('order_creditmemo', 'onclick', $onclickJs);
        $subject->updateButton(
            'order_creditmemo',
            'data_attribute',
            [
                'mage-init' => '{"Magenest_OrderCreditMemo/js/om": {}}'
            ]
        );
    }
}
