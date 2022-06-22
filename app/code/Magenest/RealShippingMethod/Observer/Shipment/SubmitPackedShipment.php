<?php
namespace Magenest\RealShippingMethod\Observer\Shipment;

use Magenest\RealShippingMethod\Model\GenerateShippingLabel;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class SubmitPackedShipment implements ObserverInterface
{
    /** @var GenerateShippingLabel */
    private $generateShippingLabel;

    /** @var ManagerInterface */
    private $messageManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param GenerateShippingLabel $generateShippingLabel
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        GenerateShippingLabel $generateShippingLabel,
        ManagerInterface      $messageManager,
        LoggerInterface       $logger
    ) {
        $this->generateShippingLabel = $generateShippingLabel;
        $this->messageManager        = $messageManager;
        $this->logger                = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        try {
            /** @var Order $order */
            $order = $observer->getEvent()->getShipment()->getOrder();
            if (in_array($order->getRealShippingMethod(), GenerateShippingLabel::ALLOWED_CARRIERS)) {
                $order->setData(
                    'shipping_method',
                    $order->getRealShippingMethod() . "_" . $order->getRealShippingMethod()
                );
                $this->generateShippingLabel->generateShippingLabel($order);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
