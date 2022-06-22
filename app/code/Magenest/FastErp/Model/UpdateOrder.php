<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 * Created by PhpStorm.
 * User: crist
 * Date: 19/11/2021
 * Time: 09:56
 */

namespace Magenest\FastErp\Model;

use Magenest\CustomSource\Helper\Data as HelperData;
use Magenest\Directory\Block\Adminhtml\Area\Field\Area;
use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Model\Order;

class UpdateOrder
{
    /**
     * @var GetAllocatedSourceCodeForOrder
     */
    protected $getAllocatedSourcesForOrder;

    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    protected $customerByArea = [];

    protected $orderByArea = [];

    protected $sourceModel;

    protected $bundleFirstItemIndex = false;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var HelperData
     */
    protected $data;

    protected $message = "";

    /**
     * UpdateOrder constructor.
     *
     * @param TimezoneInterface $timezone
     * @param ManagerInterface $eventManager
     * @param HelperData $data
     * @param Client $client
     * @param GetAllocatedSourceCodeForOrder $getAllocatedSourcesForOrder
     * @param SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        TimezoneInterface              $timezone,
        ManagerInterface               $eventManager,
        HelperData                     $data,
        Client                         $client,
        GetAllocatedSourceCodeForOrder $getAllocatedSourcesForOrder,
        SourceRepositoryInterface      $sourceRepository
    )
    {
        $this->timezone = $timezone;
        $this->client = $client;
        $this->eventManager = $eventManager;
        $this->data = $data;
        $this->getAllocatedSourcesForOrder = $getAllocatedSourcesForOrder;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @param Order $order
     *
     * @throws LocalizedException
     */
    public function execute($order)
    {
        $shippingAddress = $order->getShippingAddress();

        $orderData = [
            "magentoOrderId" => $order->getEntityId(),
            "orderNumber" => $order->getIncrementId(),
            "shippingAddressId" => $order->getShippingAddress()->getId(),
            "shippingAddress" => $this->getShippingAddressLine($order),
            "contact" => $shippingAddress->getName(),
            "paymentType" => $order->getPayment()->getMethod(),
            "paymentScheduleId" => "030",
            "orderDate" => $this->getOrderDate($order->getUpdatedAt()),
            "currencyCode" => $order->getOrderCurrencyCode(),
            "exchangeRate" => 1,
            "totalQuantity" => (int)$order->getTotalQtyOrdered(),
            "totalDiscount" => (int)$order->getDiscountAmount(),
            "total" => (int)$order->getGrandTotal() - $order->getShippingAmount(),
            "totalAfterDiscount" => (int)($order->getGrandTotal() - $order->getDiscountAmount()) - $order->getShippingAmount(),
            "invoiceCustomerName" => $order->getCompanyName(),
            "invoiceAddress" => $order->getCompanyAddress(),
            "invoiceTaxId" => $order->getTaxCode(),
            "invoiceEmail" => $order->getCompanyEmail(),
            "orderItems" => $this->getOrderItems($order),
            "customerId" => $this->getCustomerIdByArea(),
            "siteId" => $this->getAreaIdByArea(),
            "description" => implode(" ", [$order->getIncrementId(), $this->message, $shippingAddress->getName(), $shippingAddress->getTelephone(), $order->getCustomerNote(), "/ SALE"]),
        ];
        $orderData['description'] = mb_substr($orderData['description'], 0, 128);

        $result = $this->client->updateOrder($orderData);
        if (isset($result['id'])) {
            $order->setData('erp_id', $result['id']);
            $order->setStatus(UpdateOrderStatus::ERP_SYNCED_STATUS)->setState(Order::STATE_PROCESSING);
            $message = __("Order have been synced to ERP.");
        } else {
            $order->setStatus(UpdateOrderStatus::ERP_SYNCED_FAILED_STATUS)->setState(Order::STATE_PROCESSING);
            $message = "Error while sync to ERP: " . ($result['message'] ?? __("Failed to synced order to ERP"));
        }
        $order->save();
        $this->eventManager->dispatch(
            "order_management_action_dispatch_save_comment_history",
            [
                'order' => $order,
                'comment' => $message
            ]
        );
        return $result;
    }

    public function getCustomerIdByArea($area = null)
    {
        if (!$area) {
            $area = $this->sourceModel->getAreaCode();
        }
        if (!$this->customerByArea) {
            foreach ($this->data->getAreaData() as $item) {
                $this->customerByArea[$item[Area::COLUMN_AREA_CODE]] = $item[Area::COLUMN_AREA_CUSTOMER_ID];
                $this->orderByArea[$item[Area::COLUMN_AREA_CODE]] = $item[Area::COLUMN_AREA_ID];
            }
        }

        return $this->customerByArea[$area] ?? reset($this->customerByArea);
    }

    public function getAreaIdByArea($area = null)
    {
        if (!$area) {
            $area = $this->sourceModel->getAreaCode();
        }
        if (!$this->orderByArea) {
            foreach ($this->data->getAreaData() as $item) {
                $this->customerByArea[$item[Area::COLUMN_AREA_CODE]] = $item[Area::COLUMN_AREA_CUSTOMER_ID];
                $this->orderByArea[$item[Area::COLUMN_AREA_CODE]] = $item[Area::COLUMN_AREA_ID];
            }
        }

        return $this->orderByArea[$area] ?? reset($this->orderByArea);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function getShippingAddressLine($order)
    {
        $shippingAddress = $order->getShippingAddress();

        return implode(
            ", ",
            array_merge(
                $shippingAddress->getStreet(),
                [
                    $shippingAddress->getWard(),
                    $shippingAddress->getDistrict(),
                    $shippingAddress->getCity()
                ]
            )
        );
    }

    public function getOrderDate($date)
    {
        $date = $this->timezone->date(\DateTime::createFromFormat('Y-m-d h:i:s', $date));
        return $date->format('Y-m-d\Th:i:s\Z');
    }

    /**
     * @param Order $order
     */
    public function getOrderItems($order)
    {
        $itemsData = [];
        $source = $this->getAllocatedSourcesForOrder->execute($order->getId());
        if (empty($source)) {
            throw new LocalizedException(__('Order has not been assigned to any source'));
        } else {
            $source = $source[0];
        }
        $sourceModel = $this->sourceRepository->get($source);
        if ($order->getCompanyName()) {
            $this->message .= " Xuất hóa đơn VAT ";
        }
        $message = "";
        switch ($order->getRealShippingMethod()) {
            case 'giaohangtietkiem':
                $message = "GIAOHANGTIETKIEM";
                break;
            case 'viettelPostCarrier':
                if ($sourceModel->getErpSourceCode() != "VU157") {
                    $message = "VIETTELPOST";
                }
                break;
            case 'selfDelivery':
                $message = "KH Tự vận chuyển";
                break;
            default:
                $message = "KHÁC";
                break;
        }
        $this->message .= $message;

        if ($sourceModel->getErpSourceCode() == "VU157") {
            $this->message .= " Xuất từ kho VU157 ";
        }

        $index = 0;
        $countChildItemBundle = 0;

        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($order->getItems() as $idx => $item) {
            if ($item->getProductType() == Configurable::TYPE_CODE) {
                continue;
            }
            if ($item->getProductType() == BundleType::TYPE_CODE) {
                $countChildItemBundle = count($item->getChildrenItems()) ?? 0;
                $this->message .= $item->getName() . ": " . $item->getPrice();
                $this->bundleFirstItemIndex = false;
                continue;
            }
            $bundleItem = false;
            if ($item->getParentItem()) {
                $parentItem = $item->getParentItem();
                if ($parentItem->getProductType() == BundleType::TYPE_CODE) {
                    $bundleItem = true;
                }
                if ($parentItem->getProductType() != Configurable::TYPE_CODE) {
                    $parentItem = false;
                }
            } else {
                $parentItem = false;
            }

            if ($bundleItem) {
                $productOptions = $item->getProductOptions();
                $bundleSelectionAttribute = json_decode($productOptions['bundle_selection_attributes'] ?? "", true);
                $qty = $bundleSelectionAttribute['qty'] ?? 0;
                $parentItem = $item->getParentItem();

                $parentPrice = $parentItem->getPrice();
                $price = $countChildItemBundle ? $parentPrice / $countChildItemBundle : $parentPrice;
                $rowTotal = $price * $qty;

                $taxAmount = $parentItem->getTaxAmount();
                $tax = $countChildItemBundle ? $taxAmount / $countChildItemBundle : $taxAmount;
                $taxRate = floatval($parentItem->getTaxPercent());

                $discountPercent = $parentItem->getDiscountPercent();
                $discountAmountParent = $parentItem->getDiscountAmount();
                $discountAmount = $countChildItemBundle ? $discountAmountParent / $countChildItemBundle : $discountAmountParent;

                if ($this->bundleFirstItemIndex !== false && isset($itemsData[$this->bundleFirstItemIndex])) {
                    $itemsData[$this->bundleFirstItemIndex]['price'] = $price;
                    $itemsData[$this->bundleFirstItemIndex]['total'] = $rowTotal;
                }
            } else {
                $price = $parentItem ? $parentItem->getPrice() : $item->getPrice();
                $qty = $item->getQtyOrdered();
                $tax = $item->getTaxAmount();
                $rowTotal = $parentItem ? $parentItem->getRowTotal() : $item->getRowTotal();
                $discountPercent = $parentItem ? $parentItem->getDiscountPercent() : $item->getDiscountPercent();
                $discountAmount = $parentItem ? $parentItem->getDiscountAmount() : $item->getDiscountAmount();
                $taxRate = floatval($item->getTaxPercent());
            }
            $this->sourceModel = $sourceModel;
            $this->bundleFirstItemIndex = $index;

            $taxId = $taxRate == 10 ? '10' : '08';

            $itemsData[] = [
                "id" => $item->getItemId(),
                "lineNumber" => $index + 1,
                "warehouseId" => $sourceModel->getErpSourceCode() == "VU157" ? "VU100" : $sourceModel->getErpSourceCode(),
                "productId" => $item->getSku(),
                "unitId" => "Bộ",
                "quantity" => (int)$qty,
                "price" => $price,
                "total" => (int)$rowTotal,
                "discountPercentage" => (int)$discountPercent,
                "discount" => (int)($discountAmount - ($discountAmount * ($taxRate) / (100 + $taxRate))),
                "taxId" => $taxId,
                "taxRate" => $taxRate,
                "tax" => (int)$tax,
                "isPromotional" => $rowTotal == 0,
                "promotionId" => null,
            ];
            $index++;
        }

        return $itemsData;
    }
}
