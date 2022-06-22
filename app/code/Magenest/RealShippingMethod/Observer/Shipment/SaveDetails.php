<?php
namespace Magenest\RealShippingMethod\Observer\Shipment;

use Magenest\SelfDelivery\Model\Carrier\SelfDelivery;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Shipment;

class SaveDetails implements ObserverInterface
{
    /** @var RequestInterface */
    private $request;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /**
     * @param RequestInterface $request
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        RequestInterface         $request,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->request         = $request;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $carrier  = $this->request->getParam('real_shipping_method', SelfDelivery::CODE);

        $order = $shipment->getOrder();
        $order->addData([
            'real_shipping_method' => $carrier,
            'source_code'          => $this->request->getParam('pickup_source'),
            'shipping_option'      => $this->request->getParam('shipping_option'),
        ]);
        if ($carrier === "custom") {
            $order->setData('shipping_fee', $this->request->getParam('manual_fee'));
        }
        $this->orderRepository->save($order);
    }
}
