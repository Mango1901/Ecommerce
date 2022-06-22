<?php
namespace Magenest\FastErp\Model;

use Magenest\CustomSource\Helper\Data as HelperData;
use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class SyncOrders extends UpdateOrder
{
    /** @var AsyncClient */
    private $asyncClient;

    /** @var SerializerInterface */
    private $serializer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param SerializerInterface $serializer
     * @param TimezoneInterface $timezone
     * @param ManagerInterface $eventManager
     * @param LoggerInterface $logger
     * @param HelperData $data
     * @param Client $client
     * @param GetAllocatedSourceCodeForOrder $getAllocatedSourcesForOrder
     * @param SourceRepositoryInterface $sourceRepository
     * @param AsyncClient $asyncClient
     */
    public function __construct(
        SerializerInterface            $serializer,
        TimezoneInterface              $timezone,
        ManagerInterface               $eventManager,
        LoggerInterface                $logger,
        HelperData                     $data,
        Client                         $client,
        GetAllocatedSourceCodeForOrder $getAllocatedSourcesForOrder,
        SourceRepositoryInterface      $sourceRepository,
        AsyncClient                    $asyncClient
    ) {
        $this->logger      = $logger;
        $this->serializer  = $serializer;
        $this->asyncClient = $asyncClient;
        parent::__construct($timezone, $eventManager, $data, $client, $getAllocatedSourcesForOrder, $sourceRepository);
    }

    /**
     * @param Order[] $orders
     *
     * @return int
     */
    public function execute($orders)
    {
        $count     = 0;
        $responses = $this->sendRequest($orders);
        foreach ($responses as $response) {
            try {
                $body = $this->serializer->unserialize($response['res']->get()->getBody());
                if (isset($body['id'])) {
                    $count++;
                    $response['obj']
                        ->setData('erp_id', $body['id'])
                        ->setStatus(UpdateOrderStatus::ERP_SYNCED_STATUS)
                        ->setState(Order::STATE_PROCESSING);
                    $message = __("Order have been synced to ERP.");
                } else {
                    throw new LocalizedException(
                        !empty($body['errors'])
                            ? __(json_encode($body['errors'], JSON_UNESCAPED_UNICODE))
                            : __("Failed to synced order to ERP")
                    );
                }
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), [
                    'trace'    => $e->getTraceAsString(),
                    'response' => $response['res']->get()->getBody()
                ]);
                $response['obj']
                    ->setStatus(UpdateOrderStatus::ERP_SYNCED_FAILED_STATUS)
                    ->setState(Order::STATE_PROCESSING);
                $message = __("Error while sync to ERP: %1", $e->getMessage());
            }

            $response['obj']->save();
            $this->eventManager->dispatch(
                "order_management_action_dispatch_save_comment_history",
                [
                    'order'   => $response['obj'],
                    'comment' => $message
                ]
            );
        }

        return $count;
    }

    /**
     * @param Order[] $orders
     *
     * @return array
     */
    private function sendRequest($orders)
    {
        $result = [];
        foreach ($orders as $order) {
            if (!$order->getErpId() && $order->hasShipments()) {
                $shippingAddress = $order->getShippingAddress();
                try {
                    $params   = [
                        "orderNumber"         => $order->getIncrementId(),
                        "shippingAddressId"   => $order->getShippingAddress()->getId(),
                        "shippingAddress"     => $this->getShippingAddressLine($order),
                        "contact"             => $shippingAddress->getName(),
                        "paymentType"         => $order->getPayment()->getMethod(),
                        "paymentScheduleId"   => "030",
                        "orderDate"           => $this->getOrderDate($order->getUpdatedAt()),
                        "currencyCode"        => $order->getOrderCurrencyCode(),
                        "exchangeRate"        => 1,
                        "totalQuantity"       => (int)$order->getTotalQtyOrdered(),
                        "totalDiscount"       => (int)$order->getDiscountAmount(),
                        "total"               => (int)$order->getGrandTotal(),
                        "totalAfterDiscount"  => (int)($order->getGrandTotal() - $order->getDiscountAmount()),
                        "invoiceCustomerName" => $order->getCompanyName(),
                        "invoiceAddress"      => $order->getCompanyAddress(),
                        "invoiceTaxId"        => $order->getTaxCode(),
                        "invoiceEmail"        => $order->getCompanyEmail(),
                        "orderItems"          => $this->getOrderItems($order),
                        "customerId"          => $this->getCustomerIdByArea(),
                        "siteId"              => $this->getAreaIdByArea(),
                        "description"         => implode(" ", [$order->getIncrementId(), $this->message, $shippingAddress->getName(), $shippingAddress->getTelephone(), $order->getCustomerNote(), "/ SALE"]),
                    ];

                    $params['description'] = mb_substr($params['description'], 0, 128);
                    $result[] = [
                        'obj' => $order,
                        'res' => $this->asyncClient->syncOrder($params, $order->getEntityId())
                    ];
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
                }
            }
        }

        return $result;
    }
}
