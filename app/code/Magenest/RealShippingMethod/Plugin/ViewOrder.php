<?php
namespace Magenest\RealShippingMethod\Plugin;

use Magenest\RealShippingMethod\Model\GenerateShippingLabel;
use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\Order;

class ViewOrder
{
    /** @var Registry */
    private $registry;

    /** @var UrlInterface */
    private $urlBuilder;

    /**
     * @param Registry $registry
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Registry     $registry,
        UrlInterface $urlBuilder
    ) {
        $this->registry   = $registry;
        $this->urlBuilder = $urlBuilder;
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
        $this->addResubmitButton($subject, $order);

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
    protected function addResubmitButton(View $subject, Order $order)
    {
        if ($order->hasShipments() && !$order->getApiOrderId()
            && in_array($order->getRealShippingMethod(), GenerateShippingLabel::ALLOWED_CARRIERS)) {
            $onclickJs = 'window.location.href = \''
                . $this->urlBuilder->getUrl("shipment/shipment/resubmit", ['order_id' => $this->getOrder()->getId()])
                . '\'';

            $subject->addButton(
                'order_shipment_resubmit',
                [
                    'label'   => __('Resubmit Shipment'),
                    'class'   => 'primary action-secondary',
                    'id'      => 'resubmit_shipment',
                    'onclick' => $onclickJs
                ]
            );
        }
    }
}
