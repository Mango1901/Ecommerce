<?php

namespace Magenest\CustomFrontend\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\OrderService;
use Psr\Log\LoggerInterface;

/**
 * Class ProgressOrderDate
 */
class ProgressOrderDate implements ArgumentInterface
{
    const STATE_PENDING = 'pending';
    const STATE_CONFIRMED = 'confirmed';
    const STATE_SHIPPING = 'shipping';
    const STATE_PROCESSING = 'shipping';
    const STATE_COMPLETE = 'complete';
    const ENTITY_ORDER = 'order';
    const ENTITY_SHIPMENT = 'shipment';
    const ENTITY_INVOICE = 'invoice';

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Escaper
     */
    private $_escaper;

    /**
     * @var Escaper
     */
    private $timezone;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        OrderService $orderService,
        RequestInterface $request,
        Escaper $escaper,
        TimezoneInterface $timezone,
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderService = $orderService;
        $this->request = $request;
        $this->_escaper = $escaper;
        $this->timezone = $timezone;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return array
     */
    public function getProgressOrderDateOfOrder()
    {
        $result = [];
        try {
            $orderId = $this->request->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
            if ($order->getEntityId()) {
                $result[self::STATE_PENDING] = $this->dateFormatHtml($order->getCreatedAt());
                $result[self::STATE_COMPLETE] = $order->getStatus() === 'complete' ? $this->dateFormatHtml($order->getUpdatedAt()) : null;
                $commentsList = $this->orderService->getCommentsList($orderId);
                foreach ($commentsList->getItems() as $comment) {
                    $status = $comment->getStatus();
                    $entityName = $comment->getEntityName();
                    if ($this->isConfirmedState($status, $entityName, $result)) {
                        $result[self::STATE_CONFIRMED] = $this->dateFormatHtml($comment->getCreatedAt());
                    } else if ($this->isShippingState($status, $entityName, $result)) {
                        $result[self::STATE_SHIPPING] = $this->dateFormatHtml($comment->getCreatedAt());
                    }
                }
                return $result;
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return [];
    }

    /**
     * @param $date
     * @return string
     * @throws \Exception
     */
    private function dateFormatHtml($date)
    {
        $dateFormatted = $this->timezone->date(new \DateTime($date))->format('H:i d/m/Y');
        return '<div class="order-date">' .
            $this->_escaper->escapeHtml(
                __(
                    '<span class="label">Order Date:</span> %1',
                    '<span>' . $dateFormatted . '</span>'
                ),
                ['span'])
            . '</div>';
    }

    /**
     * @param string $status
     * @param string $entityName
     * @param array $result
     * @return bool
     */
    private function isConfirmedState($status, $entityName, $result)
    {
        return !isset($result[self::STATE_CONFIRMED]) &&
            $status === self::STATE_CONFIRMED &&
            $entityName == self::ENTITY_ORDER;
    }

    /**
     * @param string $status
     * @param string $entityName
     * @param array $result
     * @return bool
     */
    private function isShippingState($status, $entityName, $result)
    {
        return !isset($result[self::STATE_SHIPPING]) &&
            ($status === self::STATE_CONFIRMED || $status == self::STATE_PROCESSING) &&
            $entityName == self::ENTITY_SHIPMENT;
    }
}
