<?php
namespace Magenest\ViettelPost\Plugin\Block\Adminhtml\Order;
use Magento\Framework\View\LayoutInterface;

class View
{
    protected $shippingCarrier;

    public function __construct(
        \Magenest\ViettelPost\Model\ShippingCarrier $shippingCarrier
    )
    {
        $this->shippingCarrier = $shippingCarrier;
    }

    public function beforeSetLayout($view, LayoutInterface $layout)
    {
        $shipment = $view->getShipment();
        //if($shipment->getCarrierCode() == 'viettel_post') {
        if(true) {
            $shipmentData = $this->shippingCarrier->getShipmentData($shipment->getId());
            if($shipmentData) {
                $shipingLabelUrl = $this->shippingCarrier->getShippingLabelUrl($shipment->getId());
                if ($shipingLabelUrl) {
                    $url = $shipingLabelUrl;
                } else {
                    $url = $view->getUrl('viettelpost/shipment/printLabel', ['id' => $shipment->getId()]);
                }
                $view->addButton(
                    'print_shippinglabel',
                    [
                        'label' => __('Print ViettelPost Shipping Label'),
                        'class' => 'print-btn',
                        'onclick' => "window.open('{$url}', '_blank') && window.location.reload(true);"
                    ]
                );
            }
        }

        return [$layout];
    }
}
