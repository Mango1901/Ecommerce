<?php
namespace Magenest\OrderCancel\Plugin\Adminhtml;

use Magenest\OrderCancel\Model\Order\Source\AdminCancelReason;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\Order;
use Magento\Framework\View\LayoutInterface;

class ViewOrder
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var AdminCancelReason */
    private $reasonOption;

    /** @var Registry */
    private $registry;

    /**
     * @param Registry $registry
     * @param AdminCancelReason $cancelReason
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Registry             $registry,
        AdminCancelReason $cancelReason,
        SerializerInterface  $serializer
    ) {
        $this->registry = $registry;
        $this->serializer = $serializer;
        $this->reasonOption = $cancelReason;
    }

    /**
     * Before set layout
     *
     * @param View $subject
     * @param LayoutInterface $layout
     * @return array
     */
    public function beforeSetLayout(View $subject, LayoutInterface $layout)
    {
        $order = $this->getOrder();
        $this->handleCancelButton($subject, $order);

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
    protected function handleCancelButton(View $subject, Order $order)
    {
        if ($this->checkCancelable($order)) {
            $subject->removeButton('order_cancel');
            $onclickJs = 'jQuery(\'#order_cancel_with_reason\').om(\'showConfirmCancelDialog\', \''
                . __('Please confirm reason to cancel this order?')
                . '\', \''
                . $subject->getUrl('sales/order/cancelOrder')
                . '\', \''
                . $this->serializer->serialize($this->reasonOption->toOptionArray())
                . '\');';

            $subject->addButton(
                'order_cancel_with_reason',
                [
                    'label' => __('Cancel Order'),
                    'class' => 'cancel primary action-secondary',
                    'id' => 'order_cancel_with_reason',
                    'onclick' => $onclickJs,
                    'data_attribute' => [
                        'mage-init' => '{"Magenest_OrderCancel/js/om": {}}'
                    ]
                ]
            );
        }
    }

    /**
     * @param Order $order
     * @return bool
     */
    private function checkCancelable(Order $order)
    {
        if ($order->canCancel()) {
            return true;
        }

        $isInvoiceCancelled = false;
        if ($order->hasInvoices()) {
            foreach ($order->getInvoiceCollection() as $item) {
                if (intval($item->getState()) === Order\Invoice::STATE_CANCELED) {
                    $isInvoiceCancelled = true;
                    break;
                }
            }

            if (!$isInvoiceCancelled && !$order->hasShipments()) {
                return true;
            }
        }

        if ($order->hasShipments() && $order->canCreditmemo()) {
            return true;
        }

        return false;
    }
}
