<?php
namespace Magenest\RealShippingMethod\Controller\Adminhtml\Shipment;

use Magenest\RealShippingMethod\Model\GenerateShippingLabel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class Resubmit extends Action
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var GenerateShippingLabel */
    private $generateShippingLabel;

    /**
     * @param Context $context
     * @param GenerateShippingLabel $generateShippingLabel
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                  $context,
        GenerateShippingLabel    $generateShippingLabel,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface          $logger
    ) {
        $this->generateShippingLabel = $generateShippingLabel;
        $this->orderRepository       = $orderRepository;
        $this->logger                = $logger;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');

            if (empty($orderId)) {
                throw new LocalizedException(__('Missing required parameter(s).'));
            }

            /** @var Order $order */
            $order = $this->orderRepository->get($orderId);
            if (in_array($order->getRealShippingMethod(), GenerateShippingLabel::ALLOWED_CARRIERS)) {
                $order->setData(
                    'shipping_method',
                    $order->getRealShippingMethod() . "_" . $order->getRealShippingMethod()
                );
                $this->generateShippingLabel->generateShippingLabel($order);
            }

        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setRefererOrBaseUrl();
    }
}
