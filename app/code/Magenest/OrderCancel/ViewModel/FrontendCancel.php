<?php
namespace Magenest\OrderCancel\ViewModel;

use Magenest\OrderCancel\Model\Order\Source\FrontendCancelReason;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class FrontendCancel implements ArgumentInterface
{
    /** @var UrlInterface */
    private $urlBuilder;

    /** @var SerializerInterface */
    private $serializer;

    /** @var FrontendCancelReason */
    private $reasonOption;

    /** @var RequestInterface */
    private $request;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /**
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @param FrontendCancelReason $reasonOption
     * @param SerializerInterface $serializer
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        UrlInterface             $urlBuilder,
        RequestInterface         $request,
        FrontendCancelReason     $reasonOption,
        SerializerInterface      $serializer,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->request         = $request;
        $this->urlBuilder      = $urlBuilder;
        $this->serializer      = $serializer;
        $this->reasonOption    = $reasonOption;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Order|null $order
     *
     * @return bool
     */
    public function isCancelable(Order $order = null)
    {
        if (!isset($order)) {
            $orderId = $this->request->getParam('order_id');
            $order   = $this->orderRepository->get($orderId);
        }

        if ($this->request->getParam('instance')) {
            return false;
        }

        return ($order->getState() === Order::STATE_NEW && $order->canCancel()) ||
            in_array($order->getStatus(), ["pending_paid", "pending_payment"]);
    }

    /**
     * @param Order|null $order
     *
     * @return string
     */
    public function getCancelUrl(Order $order = null)
    {
        $orderId = isset($order) ? $order->getId() : $this->request->getParam('order_id');
        return $this->urlBuilder->getUrl('sales/order/cancelOrder', ['order_id' => $orderId]);
    }

    /**
     * @return bool|string
     */
    public function getReasonOption()
    {
        return $this->serializer->serialize($this->reasonOption->toOptionArray());
    }
}
