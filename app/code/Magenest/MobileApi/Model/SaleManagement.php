<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Api\SaleManagementInterface;
use Magenest\OrderCancel\Block\Order\History;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Framework\DataObjectFactory;
use Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory as SearchResultFactory;

/**
 * Class SaleManagement
 * @package Magenest\MobileApi\Model
 */
class SaleManagement implements SaleManagementInterface
{
    /**
     * @var OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var OrderConfig
     */
    protected $_orderConfig;

    /**
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory;

    /** @var SearchResultFactory */
    protected $searchResultFactory = null;

    /** @var JoinProcessorInterface */
    private $extensionAttributesJoinProcessor;

    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    /**
     * @var CollectionFactoryInterface
     */
    private $collectionFactory;

    /**
     * SaleManagement constructor.
     *
     * @param OrderRepository $orderRepository
     * @param CollectionFactory $orderCollectionFactory
     * @param OrderConfig $orderConfig
     * @param DataObjectFactory $dataObjectFactory
     * @param SearchResultFactory $searchResultFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactoryInterface $collectionFactory
     */
    public function __construct(
        OrderRepository              $orderRepository,
        CollectionFactory            $orderCollectionFactory,
        OrderConfig                  $orderConfig,
        DataObjectFactory            $dataObjectFactory,
        SearchResultFactory          $searchResultFactory,
        JoinProcessorInterface       $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactoryInterface   $collectionFactory
    ) {
        $this->_orderRepository                 = $orderRepository;
        $this->_orderCollectionFactory          = $orderCollectionFactory;
        $this->_orderConfig                     = $orderConfig;
        $this->_dataObjectFactory               = $dataObjectFactory;
        $this->searchResultFactory              = $searchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor              = $collectionProcessor;
        $this->collectionFactory                = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function getOrderShipments($orderId)
    {
        return $this->_orderRepository->get($orderId)->getShipmentsCollection();
    }

    /**
     * @inheritdoc
     */
    public function getOrderInvoices($orderId)
    {
        return $this->_orderRepository->get($orderId)->getInvoiceCollection();
    }

    /**
     * @inheritdoc
     */
    public function getOrders($customerId, string $page = null, string $status = null)
    {
        if ($this->_orderCollectionFactory === null) {
            $this->_orderCollectionFactory = $this->collectionFactory;
        }

        $statuses = isset($status) && !empty(History::STATUSES[$status]['status'])
            ? History::STATUSES[$status]['status']
            : $this->_orderConfig->getVisibleOnFrontStatuses();

        $collection = $this->_orderCollectionFactory->create($customerId)
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', ['in' => $statuses])
            ->setCurPage($page ?? 1)
            ->setPageSize(10)
            ->setOrder('created_at', 'desc');
        $result     = ['total_count' => $collection->getSize()];
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection as $order) {
            $result['items'][] = [
                'entity_id'       => $order->getId(),
                'increment_id'    => $order->getRealOrderId(),
                'shipping_name'   => !$order->getIsVirtual() ? $order->getShippingAddress()->getName() : '',
                'created_at'      => $order->getCreatedAt(),
                'status_label'    => __($order->getStatusLabel()),
                'grand_total'     => $order->getGrandTotal(),
                'thumbnail'       => $this->getOrderThumbnail($order),
                'is_instalment'   => $order->getIsInstalment(),
                'is_subscription' => $order->getIsSubscription()
            ];
        }

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $result])
            ->getData();
    }

    /**
     * @param Order $order
     *
     * @return string|null
     */
    private function getOrderThumbnail(Order $order)
    {
        /** @var Order\Item $item */
        $item = $order->getItemsCollection()->getFirstItem();
        return $item->getProduct()->getMediaGalleryImages()->getFirstItem()->getUrl();
    }

    /**
     * Lists orders that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     *
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface Order search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultFactory->create();
        $this->extensionAttributesJoinProcessor->process($searchResult);
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }
}
