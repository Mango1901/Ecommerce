<?php

namespace Magenest\MasOffer\Helper;

use Magenest\OrderCancel\Block\Order\Info\ReasonCancel;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Store\Model\ScopeInterface;

/**
 * Class MasOffer
 *
 * @package DrinkiesLocal\Main\Helper
 */
class MasOffer extends AbstractHelper
{
    /**
     *
     */
    const IS_MAS_OFFER = 'is_mas_offer';
    /**
     *
     */
    const AFF_ID_MAS_OFFER = 'aff_id_mas_offer';

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $_zendClientFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $adapter;

    /**
     * MasOffer constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\HTTP\ZendClientFactory $zendClientFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection        $adapter,
        Context                                          $context,
        \Magento\Framework\HTTP\ZendClientFactory        $zendClientFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        TimezoneInterface                                $timezone
    )
    {
        parent::__construct($context);
        $this->_zendClientFactory = $zendClientFactory;
        $this->_cookieManager = $cookieManager;
        $this->_timezone = $timezone;
        $this->adapter = $adapter;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function postBack($order)
    {
        if (strtolower((string)$this->_cookieManager->getCookie('mo_network')) == 'masoffer'
            and ($affId = $this->_cookieManager->getCookie('mo_traffic_id'))) {
            try {
                //Send POST request when an order is successful
                if ($order->getIsMasOffer() == 0) {
                    $items = [];
                    $status = $order->getStatus();
                    /** @var Item $item */
                    foreach ($order->getAllVisibleItems() as $item) {
                        $itemPrice = (($item->getRowTotalInclTax() - $item->getDiscountAmount()) / $item->getQtyOrdered());
                        $items[] = [
                            'sku' => str_replace(' ', '_', $item->getSku()),
                            'url' => $item->getProduct()->getProductUrl(),
                            'price' => (($item->getRowTotalInclTax() - $item->getDiscountAmount()) / $item->getQtyOrdered()),
                            'name' => $item->getName(),
                            'category' => $item->getProduct()->getCategory(),
                            'category_id' => 1,
                            'status_code' => $status,
                            'quantity' => (int)$item->getQtyOrdered(),
                            'partner_id' => $order->getCouponCode(),
                            'status_message' => $order->getCustomerNote(),
                        ];
                    }
                    $data = [
                        "offer_id" => "kangaroo",
                        "signature" => "K1s4SQ6HD71cDiQ7",
                        "transaction_id" => $order->getIncrementId(),
                        "transaction_time" => strtotime($item->getCreatedAt()) * 1000,
                        "traffic_id" => $affId,
                        "items" => $items
                    ];
                    $this->placeRequest($data);
                    $this->saveMasOfferData(self::IS_MAS_OFFER, 1, $order->getId());
                    $this->saveMasOfferData(self::AFF_ID_MAS_OFFER, $affId, $order->getId());
                }
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
    }

    /** Get status or order (canceled, processing, complete)
     * @param \Magento\Sales\Model\Order $order
     */
    public function getStatus($order)
    {
        $state = $order->getState();
        if ($state == "Complete") {
            return 1;
        } else if ($state == "Processing") {
            return 0;
        } else if ($state == "Canceled") {
            return -1;
        }
        return 0;
    }

    /**
     * @param Order $order
     */
    public function getOrderId($order){
        return $order->getId();
    }

    /**
     * @param Order $order
     * @param ReasonCancel
     * @return string
     */
    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function postBackCompleteOrder($order)
    {
        if ($affId = $order->getAffIdMasOffer())
            try {
                $items = [];
                foreach ($order->getAllVisibleItems() as $item) {
                    $items[] = [
                        "id" => $item->getProductId(),
                        "status" => 1,
                    ];
                }
                $data = [
                    "transaction_id" => $order->getIncrementId(),
                    "status" => 1,
                    "items" => $items
                ];
                //Send PUT request when the state of order change to complete
                $this->placeRequest($data, 'PUT');
                $this->saveMasOfferData(self::IS_MAS_OFFER, 2, $order->getId());
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function postBackCancelOrder($order)
    {
        if ($affId = $order->getAffIdMasOffer())
            try {
                $items = [];
                foreach ($order->getAllVisibleItems() as $item) {
                    $items[] = [
                        "id" => $item->getProductId(),
                        "status" => 2,
                    ];
                }
                $data = [
                    "transaction_id" => $order->getIncrementId(),
                    "status" => 2,
                    "items" => $items,
                    "rejected_reason" => "[HPI] SYSTEM CANCEL",
                ];
                //Send PUT request when the state of order change to complete
                $this->placeRequest($data, 'PUT');
                $this->saveMasOfferData(self::IS_MAS_OFFER, 2, $order->getId());
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag('mas_offer/general/enabled', ScopeInterface::SCOPE_WEBSITES);
    }

    /**
     * @param $data
     * @param string $method
     * @throws \Zend_Http_Client_Exception
     */
    public function placeRequest($data, $method = 'POST')
    {
        /** @var ZendClient $client */
        $client = $this->_zendClientFactory->create();
        $client->setUri('http://s2s.dev.masoffer.tech/v1/kangaroo/transactions');
        $client->setRawData(json_encode($data, JSON_UNESCAPED_UNICODE), 'application/json');
        $this->_logger->critical(json_encode($data, JSON_UNESCAPED_UNICODE));
        $client->setHeaders([
            'Content-Type' => 'application/json'
        ]);
        $response = $client->request($method);
        $this->_logger->critical($response->getRawBody());
    }

    /**
     * @param $column
     * @param $value
     * @param $orderId
     */
    public function saveMasOfferData($column, $value, $orderId)
    {
        $tables = ['sales_order',
            'sales_order_grid'];
        foreach ($tables as $table) {
            $bind[$column] = $value;
            $where = ['entity_id = ?' => (int)$orderId];
            $connection = $this->adapter->getConnection();
            $connection->update($this->adapter->getTableName($table), $bind, $where);
        }
    }
}
