<?php

namespace Magenest\PhotoReview\ViewModel;

use Exception;
use Magenest\PhotoReview\Model\ResourceModel\Review\Collection;
use Magenest\PhotoReview\Model\ResourceModel\Review\CollectionFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Review\Model\Review;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Item;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Helper\Image;

class GetReview implements ArgumentInterface
{
    /** @var CollectionFactory */
    private $reviewCollection;

    /** @var Item */
    private $orderItemResources;

    /** @var LoggerInterface */
    private $logger;

    /** @var Image */
    private $image;

    /**
     * @param CollectionFactory $reviewCollection
     * @param LoggerInterface $logger
     * @param Item $orderItemResources
     */
    public function __construct(
        CollectionFactory $reviewCollection,
        LoggerInterface $logger,
        Item $orderItemResources,
        Image $image
    ) {
        $this->logger = $logger;
        $this->reviewCollection = $reviewCollection;
        $this->orderItemResources = $orderItemResources;
        $this->image = $image;
    }

    /**
     * @param Order $order
     *
     * @return int
     */
    public function fetchSize(Order $order)
    {
        return $this->customerReviewedCollectionOfOrder($order)->getSize();
    }

    /**
     * @param $order
     * @return Collection
     */
    private function customerReviewedCollectionOfOrder($order)
    {
        return $this->reviewCollection->create()
            ->addStatusFilter(Review::STATUS_APPROVED)
            ->addCustomerFilter($order->getCustomerId())
            ->addEntityFilter('product', $this->getProducts($order->getId()))
            ->addFieldToFilter('created_at', ['gteq' => $order->getCreatedAt()])
            ->setDateOrder();
    }

    /**
     * @param $orderId
     *
     * @return array
     */
    private function getProducts($orderId)
    {
        try {
            $select = $this->orderItemResources->getConnection()->select()
                ->from($this->orderItemResources->getMainTable(), OrderItemInterface::PRODUCT_ID)
                ->where(OrderItemInterface::ORDER_ID . " = ?", $orderId);
            return $this->orderItemResources->getConnection()->fetchCol($select);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [];
        }
    }

    /**
     * @param Order $order
     * @return array
     */
    public function getAllItemNeedReview(Order $order)
    {
        $result = [];
        try {
            $customerReviewedCollection = $this->customerReviewedCollectionOfOrder($order);

            foreach ($order->getItems() as $item) {
                $isReviewed = false;
                foreach ($customerReviewedCollection->getItems() as $viewedItem) {
                    if ($viewedItem->getEntityPkValue() == $item->getProductId()) {
                        $isReviewed = true;
                        break;
                    }
                }

                $result[] = [
                    'id' => $item->getProductId(),
                    'product_name' => $item->getName(),
                    'product_image' => $this->image->init($item, 'product_base_image')
                        ->setImageFile($item->getProduct() ? $item->getProduct()->getImage() : null)
                        ->getUrl(),
                    'product_sku' => $item->getSku(),
                    'status' => $isReviewed,
                    'review_url' => !$isReviewed ? $this->getUrlReviewProduct($item) : null
                ];
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }

    /**
     * @param Order\Item $item
     * @return string|null
     */
    public function getUrlReviewProduct($item)
    {
        try {
            return $item->getProduct()->getProductUrl() . '#reviews';
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return null;
    }
}
