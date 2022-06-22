<?php

namespace Magenest\GiaoHangTietKiem\Model\Carrier;

use Exception;
use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Checkout\Model\Session;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Xml\Security;
use Magento\Inventory\Model\ResourceModel\Source;
use Magento\Inventory\Model\SourceFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory as RateErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\ResourceModel\Quote;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as ResourceOrder;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory as RateFactory;
use Magento\Shipping\Model\Simplexml\ElementFactory;
use Magento\Shipping\Model\Tracking\Result\ErrorFactory as TrackErrorFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Magento\Shipping\Model\Tracking\ResultFactory as TrackFactory;
use Psr\Log\LoggerInterface;
use Zend_Http_Client;

class GiaoHangTietKiem extends AbstractCarrierOnline implements CarrierInterface
{
    const CODE = 'giaohangtietkiem';
    const KEY_XFAST_SERVICE = 'xfast';

    /** @var string */
    protected $_code = 'giaohangtietkiem';

    /** @var bool */
    protected $_isFixed = true;

    /** @var ZendClientFactory */
    protected $httpClientFactory;

    /** @var SourceFactory */
    protected $sourceFactory;

    /** @var Quote */
    protected $inventoryResource;

    /** @var MessageManager */
    protected $messageManager;

    /** @var ResourceOrder */
    protected $resourceOrder;

    /** @var OrderFactory */
    protected $orderFactory;

    /** @var Session */
    protected $checkoutSession;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var CacheInterface */
    protected $cache;

    /** @var NotifierInterface */
    protected $notifier;

    /** @var EventManager */
    protected $eventManager;

    /** @var TimezoneInterface */
    protected $timezone;

    protected $api;

    /**
     * @var \Magenest\CustomSource\Helper\Data
     */
    protected $areaData;

    /**
     * GiaoHangTietKiem constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param RateErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param ElementFactory $xmlElFactory
     * @param RateFactory $rateFactory
     * @param MethodFactory $rateMethodFactory
     * @param TrackFactory $trackFactory
     * @param TrackErrorFactory $trackErrorFactory
     * @param StatusFactory $trackStatusFactory
     * @param RegionFactory $regionFactory
     * @param CountryFactory $countryFactory
     * @param CurrencyFactory $currencyFactory
     * @param Data $directoryData
     * @param StockRegistryInterface $stockRegistry
     * @param ZendClientFactory $httpClientFactory
     * @param Source $inventoryResource
     * @param SourceFactory $sourceFactory
     * @param MessageManager $messageManager
     * @param ResourceOrder $resourceOrder
     * @param OrderFactory $orderFactory
     * @param Session $session
     * @param SerializerInterface $serializer
     * @param CacheInterface $cache
     * @param NotifierInterface $notifier
     * @param EventManager $eventManager
     * @param TimezoneInterface $timezone
     * @param HttpContext $context
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface               $scopeConfig,
        RateErrorFactory                   $rateErrorFactory,
        LoggerInterface                    $logger,
        Security                           $xmlSecurity,
        ElementFactory                     $xmlElFactory,
        RateFactory                        $rateFactory,
        MethodFactory                      $rateMethodFactory,
        TrackFactory                       $trackFactory,
        TrackErrorFactory                  $trackErrorFactory,
        StatusFactory                      $trackStatusFactory,
        RegionFactory                      $regionFactory,
        CountryFactory                     $countryFactory,
        CurrencyFactory                    $currencyFactory,
        Data                               $directoryData,
        StockRegistryInterface             $stockRegistry,
        ZendClientFactory                  $httpClientFactory,
        Source                             $inventoryResource,
        SourceFactory                      $sourceFactory,
        MessageManager                     $messageManager,
        ResourceOrder                      $resourceOrder,
        OrderFactory                       $orderFactory,
        Session                            $session,
        SerializerInterface                $serializer,
        CacheInterface                     $cache,
        NotifierInterface                  $notifier,
        EventManager                       $eventManager,
        TimezoneInterface                  $timezone,
        \Magenest\CustomSource\Helper\Data $areaData,
        array                              $data = []
    )
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->inventoryResource = $inventoryResource;
        $this->sourceFactory = $sourceFactory;
        $this->messageManager = $messageManager;
        $this->resourceOrder = $resourceOrder;
        $this->orderFactory = $orderFactory;
        $this->checkoutSession = $session;
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->notifier = $notifier;
        $this->eventManager = $eventManager;
        $this->timezone = $timezone;
        $this->areaData = $areaData;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param array $data
     *
     * @throws AlreadyExistsException
     */
    public function updateShipmentStatus($data = [])
    {
        if (!empty($data) && isset($data['label_id']) && isset($data['partner_id'])) {
            $partnerId = $data['partner_id'];
            $order = $this->orderFactory->create();
            $this->resourceOrder->load($order, $partnerId, 'increment_id');
            if (!$order->getId()) {
                return;
            }
            $order->setApiOrderId($data['label_id']);
            $order->setShippingStatus($data['status_id']);
            if ($data['status_id'] == 5) {
                $order->setStatus(Order::STATE_COMPLETE)->setState(Order::STATE_COMPLETE);
                if ($order->getPayment()->getMethod() == "cashondelivery") {
                    $codAmount = $order->getGrandTotal();
                    $order->setTransferAmount($codAmount);
                    $order->setTransferDate($this->timezone->date()->format('Y-m-d H:i:s'));
                }
            } elseif ($data['status_id'] == -1) {
                $reason = null;
                if (isset($data['reason']) && $data['reason']) {
                    $reason = $data['reason'];
                }
            }
            $this->resourceOrder->save($order);
        }
    }

    /**
     * @param $params
     * @param $ghtkLabelId
     */
    public function cancelOrder($params, $ghtkLabelId)
    {
        $uri = $this->getModeUrl() . "/services/shipment/cancel/" . $ghtkLabelId;
        $this->apiCall('POST', $params, $uri);
    }

    public function getModeUrl()
    {
        if ($this->getConfigData('test_mode')) {
            return $this->getConfigData('sandbox_api');
        } else {
            return $this->getConfigData('production_api');
        }
    }

    public function apiCall($method, $params, $uri, $cacheKey = false)
    {
        $cacheData = $this->cache->load($cacheKey);
        if (!$cacheData || !$cacheKey) {
            $client = $this->httpClientFactory->create();
            $client->setUri($uri);
            $client->setMethod($method);
            $client->setHeaders(Zend_Http_Client::CONTENT_TYPE, 'application/json');
            $client->setHeaders('Accept', 'application/json');
            if (isset($params['token'])) {
                $client->setHeaders('Token', $params['token']);
                unset($params['token']);
            }
            if ($method == 'GET') {
                $client->setParameterGet($params);
            } elseif ($method == 'POST') {
                $client->setParameterPost($params);
            } else {
                $client->setRawData($params);
            }
            $response = $client->request();
            $cacheData = $response->getBody();
            if ($cacheKey && preg_match("/\b2[0-9]{2}\b/", $response->getStatus()) === 1) {
                $this->cache->save($cacheData, $cacheKey, ['config'], 3600);
            }
        }

        return $cacheData;
    }

    public function processAdditionalValidation(DataObject $request)
    {
        return true;
    }

    public function isShippingLabelsAvailable()
    {
        return true;
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = $this->getFreeBoxesCount($request);

        $shippingPrice = $this->ghtkShippingPrice($request);

        $result = $this->_rateFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getPackageQty() == $freeBoxes || $request->getFreeShipping()) {
                $shippingPrice = '0.00';
            }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
            $this->checkoutSession->setActiveShippingMethod($this->_code);
            $result->append($method);
        }

        return $result;
    }

    private function getFreeBoxesCount(RateRequest $request)
    {
        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    $freeBoxes += $this->getFreeBoxesCountFromChildren($item);
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        return $freeBoxes;
    }

    /**
     * Returns free boxes count of children
     *
     * @param mixed $item
     *
     * @return mixed
     */
    private function getFreeBoxesCountFromChildren($item)
    {
        $freeBoxes = 0;
        foreach ($item->getChildren() as $child) {
            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                $freeBoxes += $item->getQty() * $child->getQty();
            }
        }
        return $freeBoxes;
    }

    /**
     * @param $request
     *
     * @return int|mixed
     */
    public function ghtkShippingPrice($request)
    {
        $token = $this->getConfigData('api_token');
        $sourceItem = $this->sourceFactory->create();
        $areaCode = $this->areaData->getCurrentArea();
        if ($areaCode === 'mien_bac') {
            $this->inventoryResource->load($sourceItem, 'hy', 'source_code');
        } else {
            $this->inventoryResource->load($sourceItem, $areaCode, 'area_code');
        }

        $items = $request->getAllItems();
        $weight = 0;
        foreach ($items as $item) {
            if ($item->getWeight()) {
                $weight += ($item->getWeight() * $item->getQty() * 1000);
            }
        }
        $district = $request->getDestDistrict();
        if (is_array($district)) {
            $district = $district[1] ?? $district;
        }

        $params = [
            "token" => $token,
            "pick_address" => $sourceItem->getDetailAddress(),
            "pick_province" => $sourceItem->getCity(),
            "pick_district" => $sourceItem->getDistrict(),
            "pick_ward" => $sourceItem->getWard(),
            "province" => $request->getDestCity(),
            "district" => $district,
            "address" => $request->getDestStreet(),
            "weight" => $weight,
            "value" => $request->getPackageValue(),
            "delivery_option" => $this->getConfigData('xfast') ? 'xteam' : 'none'
        ];

        $cacheKey = "{$sourceItem->getCity()}-{$request->getDestCity()}-{$district}-{$request->getDestStreet()}-{$weight}-{$request->getPackageValue()}";
        $shippingPrice = 0;

        $uri = $this->getModeUrl() . '/services/shipment/fee';
        $this->_logger->debug('GHTK Price request: ' . $this->serializer->serialize($params));
        $responseApi = $this->apiCall('GET', $params, $uri, $cacheKey);
        $this->_logger->debug('GHTK Price response: ' . $responseApi);
        $response = $this->serializer->unserialize($responseApi);
        if (isset($response['success']) && $response['success'] == true) {
            $shippingPrice = $response['fee']['fee'];
        }

        return $shippingPrice;
    }

    /**
     * Simplified shipping price calculation request
     *
     * @param $request
     *
     * @return array|bool|float|int|string|null
     */
    public function getRates($request)
    {
        $token = $this->getConfigData('api_token');
        $params = [
            "token" => $token,
            "pick_province" => $request->getSourceCity(),
            "pick_district" => $request->getSourceDistrict(),
            "pick_ward" => $request->getSourceWard(),
            "province" => $request->getDestCity(),
            "district" => $request->getDestDistrict(),
            "weight" => $request->getWeight(),
            "value" => $request->getPackageValue(),
            "delivery_option" => "none"
        ];

        $uri = $this->getModeUrl() . '/services/shipment/fee';
        $cacheKey = "GHTK-{$request->getSourceCityId()}-" . "{$request->getSourceDistrictId()}-"
            . "{$request->getDestCityId()}-" . "{$request->getDestDistrictId()}-"
            . "{$request->getWeight()}-" . "{$request->getPackageValue()}";
        $responseApi = $this->apiCall('GET', $params, $uri, $cacheKey);
        return $this->serializer->unserialize($responseApi);
    }

    public function returnOfShipment($request)
    {
        $request->setIsReturn(true);
        //        $data = [];
        //        $result = $this->_doShipmentRequest($request);
        //
        //        if ($result->hasErrors()) {
        //            $this->rollBack($data);
        //        } else {
        //            $data[] = [
        //                'tracking_number' => $result->getTrackingNumber(),
        //                'label_content' => $result->getShippingLabelContent(),
        //                'shipping_api_id' => $result->getShippingApiId()
        //            ];
        //        }

        $time = time();
        $data[] = [
            'tracking_number' => "NA-" . $time,
            'label_content' => "Not Active",
            'shipping_api_id' => "NA-" . $time
        ];

        if (!isset($isFirstRequest)) {
            $request->setMasterTrackingId("NA-" . $time);
            $isFirstRequest = false;
        }
        return new DataObject(['info' => $data]);
    }

    /**
     * @param DataObject $request
     *
     * @return DataObject
     * @throws AlreadyExistsException
     */
    protected function _doShipmentRequest(DataObject $request)
    {
        $result = new DataObject();
        if ($request->getIsReturn()) {
            $result->setTrackingNumber($request->getOrder()->getId());
        } else {
            $result->setTrackingNumber($request->getOrderShipment()->getOrderId());
        }
        $result->setShippingLabelContent('GHTK Service');
        $packagesWeight = 0;
        if ($request->getIsReturn()
            || ($request->getOrderShipment()
                && !$request->getOrderShipment()->getOrder()->getApiOrderId())) {
            $packages = $request->getPackages();
            foreach ($packages as $package) {
                $packagesWeight += $package['params']['weight'];
            }
            $this->createOrder($request, $result, $packagesWeight);
        }

        return $result;
    }

    /**
     * @param $request
     * @param $result
     * @param $packagesWeight
     *
     * @throws AlreadyExistsException
     */
    public function createOrder($request, $result, $packagesWeight)
    {
        $order = $request->getIsReturn() ? $request->getOrder() : $request->getOrderShipment()->getOrder();
        $id = $request->getIsReturn() ? sprintf('RMA-%09d', $request->getRmaId()) : $order->getLsOrderWebId();

        try {
            /** Get config value */
            $token = $this->getConfigData('api_token');
            /** get District */
            $sourceItem = $this->sourceFactory->create();
            $this->inventoryResource->load($sourceItem, $order->getSourceCode(), 'source_code');
            $params = $request->getIsReturn()
                ?
                $this->getReturnOrderParams($order, $sourceItem, $packagesWeight, $token, $request->getRmaId())
                :
                $this->getDeliveryOrderParams($order, $sourceItem, $packagesWeight, $token);
            if ($pickSessions = $this->getXfastAvai($order, $sourceItem, $token)) {
                $params['pick_option'] = 'cod';
                $params['deliver_option'] = 'xteam';
                $pickOption = array_shift($pickSessions);
                $params['pick_session'] = $pickOption['session_val'] ?? null;
            }
            $this->_logger->debug('GHTK Order Request: ' . $this->serializer->serialize($params));
            $uri = $this->getModeUrl() . '/services/shipment/order';
            $response = $this->serializer->unserialize($this->apiCall('POST', $params, $uri));
            $this->_logger->debug('GHTK Order Response: ' . $this->serializer->serialize($response));

            if (!$response) {
                if (!$request->getIsReturn()) {
                    $order->setState(Order::STATE_PROCESSING);
                    $this->resourceOrder->save($order);
                }
                $this->messageManager->addErrorMessage("Đơn hàng $id sẽ được cập nhật sau do hệ thống GHTK quá tải.");
            } elseif (isset($response['success']) && $response['success'] == 'true') {
                $shippingFee = $response['order']['fee'];
                $orderCode = $response['order']['label'];
                if (!$request->getIsReturn()) {
                    $order->setData('api_order_id', $orderCode);
                    $order->setData('shipping_fee', $shippingFee);
                    $order->setShippingStatus($response['order']['status_id']);
                    $order->setState(Order::STATE_PROCESSING);
                    $order->setStatus(UpdateOrderStatus::PROCESSING_SHIPMENT_STATUS);
                    $this->resourceOrder->save($order);
                    $this->eventManager->dispatch("create_ghtk_success", ['order' => $order]);
                }
                $result->setShippingApiId($orderCode);
                $this->messageManager->addSuccessMessage(__("Create Giao Hang Tiet Kiem Order %1 Success", $id));
            } else {
                throw new LocalizedException(__($response['message']));
            }
        } catch (Exception $e) {
            $this->notifier->addNotice(
                __('Submit GHTK'),
                __(
                    "Unable to submit Giao Hang Tiet Kiem for order: %1. Error: %2",
                    $order->getIncrementId(),
                    $e->getMessage()
                )
            );
            $this->eventManager->dispatch(
                "order_action_save_comment_history",
                [
                    'order' => $order,
                    'comment' => __("Create Giao Hang Tiet Kiem Order Fail: %1", $e->getMessage())
                ]
            );
            $result->setErrors(__("Create Giao Hang Tiet Kiem Order %1 Fail: %2", $id, $e->getMessage()));
        }
    }

    public function getReturnOrderParams($order, $sourceItem, $packagesWeight, $token, $rmaId)
    {
        $customerTelephone = $order->getShippingAddress()->getTelephone();
        if ($customerTelephone[0] != "0") {
            $customerTelephone = "0" . $customerTelephone;
        }
        $sourceTelephone = $sourceItem->getPhone();
        if ($sourceTelephone[0] != "0") {
            $sourceTelephone = "0" . $sourceTelephone;
        }
        $items = $order->getItems();
        $data = [];
        $weight = 0;
        foreach ($items as $item) {
            if ($item->getParentItemId() == null) {
                $itemWeight = (float)$item->getWeight() != 0 ? $item->getWeight() : 0.3;
                $data[] = [
                    "name" => $item->getName(),
                    "weight" => $itemWeight * $item->getQtyOrdered(),
                    "quantity" => (int)$item->getQtyOrdered()
                ];
                $weight += ($itemWeight * $item->getQtyOrdered());
            }
        }

        $isFreeShip = 1;
        $address = $order->getShippingAddress()->getStreet()[0];
        $province = $order->getShippingAddress()->getCity();
        $district = $order->getShippingAddress()->getDistrict();
        $ward = $order->getShippingAddress()->getWard();

        $hamlet = in_array($order->getShippingAddress()->getCityId(), [7, 13, 56])
            ? $this->getLevel4Address($province, $district, $ward, $order->getShippingAddress()->getStreet())
            : "Khác";

        return [
            "token" => $token,
            "order" => [
                "id" => sprintf('RMA-%09d', $rmaId),
                "pick_money" => 0,
                "pick_name" => $order->getCustomerName(),
                "pick_tel" => $customerTelephone,
                "pick_address" => $address,
                "pick_province" => $province,
                "pick_district" => $district,
                "pick_ward" => $ward,
                "name" => $order->getShippingAddress()->getName(),
                "tel" => $sourceTelephone,
                "address" => $sourceItem->getStreet(),
                "province" => $sourceItem->getCity(),
                "district" => $sourceItem->getDistrict(),
                "ward" => $sourceItem->getWard(),
                "hamlet" => $hamlet,
                "email" => $sourceItem->getEmail(),
                "return_name" => $order->getShippingAddress()->getCustomerName(),
                "return_address" => $address,
                "return_province" => $province,
                "return_district" => $district,
                "return_tel" => $customerTelephone,
                "return_email" => $order->getCustomerEmail(),
                "is_freeship" => $isFreeShip,
                "total_weight" => ($weight != 0) ? $weight : $packagesWeight,
                "value" => (int)$order->getSubtotal(),
                "transport" => "road"
            ],
            "products" => $data
        ];
    }

    /**
     * @param $city
     * @param $district
     * @param $ward
     * @param $street
     *
     * @return false|mixed
     * @throws LocalizedException
     */
    public function getLevel4Address($city, $district, $ward, $street)
    {
        $uri = $this->getModeUrl() . "/services/address/getAddressLevel4";
        if (is_array($street)) {
            $street = implode(", ", $street);
        }
        $result = $this->ApiCall('POST', [
            'token' => $this->getConfigData('api_token'),
            'province' => $city,
            'district' => $district,
            'ward_street' => $ward,
            'street' => $street
        ], $uri);

        $address = $this->serializer->unserialize($result);
        $closestPercent = 0;
        $closestAddress = false;
        if (isset($address['data'])) {
            foreach ($address['data'] as $lvl4Address) {
                similar_text($lvl4Address, $street . " " . $ward, $percent);
                if ($closestPercent < $percent) {
                    $closestPercent = $percent;
                    $closestAddress = $lvl4Address;
                }
            }
        }
        if ($closestPercent < 20) {
            return $closestAddress;
        }

        throw new LocalizedException(__("Cannot find GiaoHangTietKiem hamlet details for this order"));
    }

    public function getDeliveryOrderParams($order, $sourceItem, $packagesWeight, $token)
    {
        $telephone = $order->getShippingAddress()->getTelephone();
        if ($telephone[0] != "0") {
            $telephone = "0" . $telephone;
        }
        $clientTelephone = $sourceItem->getPhone();
        if ($clientTelephone[0] != "0") {
            $clientTelephone = "0" . $clientTelephone;
        }

        $data = [];
        $weight = 0;

        // Calculate total weight of order items
        foreach ($order->getItems() as $item) {
            if (empty($item->getChildrenItems()) && $item->getParentItemId() == null) {
                $this->processWeight($item, $weight);
            } else {
                foreach ($item->getChildrenItems() as $child) {
                    $this->processWeight($child, $weight);
                }
            }
        }

        $totalWeight = ($weight != 0) ? $weight : $packagesWeight;

        // Handle params product data
        foreach ($order->getItems() as $item) {
            if (empty($item->getChildrenItems()) && $item->getParentItemId() == null) {
                $this->processItem($item, $data, $totalWeight);
            } else {
                foreach ($item->getChildrenItems() as $child) {
                    $this->processItem($child, $data, $totalWeight);
                }
            }
        }

        $isFreeShip = 1;
        $address = $order->getShippingAddress()->getStreet()[0];
        $province = $order->getShippingAddress()->getCity();
        $district = $order->getShippingAddress()->getDistrict();
        $street = $order->getShippingAddress()->getStreet()[0];
        $ward = $order->getShippingAddress()->getWard();
        $pickMoney = (int)$order->getGrandTotal();

        $hamlet = in_array($order->getShippingAddress()->getCityId(), [7, 13, 56])
            ? $this->getLevel4Address($province, $district, $ward, $order->getShippingAddress()->getStreet())
            : "Khác";

        $params = [
            "token" => $token,
            "order" => [
                "id" => $order->getIncrementId(),
                "pick_money" => $order->getPayment()->getMethod() == "cashondelivery" ? $pickMoney : 0,
                "pick_name" => $sourceItem->getName() ? $sourceItem->getName() : "Kangaroo",
                "pick_tel" => $clientTelephone,
                "pick_address" => $sourceItem->getStreet(),
                "pick_province" => $sourceItem->getCity(),
                "pick_district" => $sourceItem->getDistrict(),
                "pick_ward" => $sourceItem->getWard(),
                "name" => $order->getShippingAddress()->getName(),
                "tel" => $telephone,
                "address" => $address,
                "province" => $province,
                "district" => $district,
                "ward" => $ward,
                "street" => $street,
                "hamlet" => $hamlet,
                "email" => $order->getShippingAddress()->getEmail(),
                "return_name" => $sourceItem->getName() ? $sourceItem->getName() : "Kangaroo",
                "return_address" => $sourceItem->getStreet(),
                "return_province" => $sourceItem->getCity(),
                "return_district" => $sourceItem->getDistrict(),
                "return_tel" => $clientTelephone,
                "return_email" => $sourceItem->getEmail(),
                "is_freeship" => $isFreeShip,
                "total_weight" => $totalWeight,
                "note" => $order->getCustomerNote(),
                "value" => (int)$order->getSubtotal(),
                "transport" => "road",
                "3pl" => $this->isTotalWeightGreaterThan20($totalWeight) ? 1 : null
            ],
            "products" => $data
        ];

        return $params;
    }

    /**
     * @param $item
     * @param $data
     * @param $weight
     *
     * @return void
     */
    private function processWeight($item, &$weight)
    {
        $itemWeight = (float)$item->getWeight() != 0 ? $item->getWeight() : 0.3;
        $weight += ($itemWeight * $item->getQtyOrdered());
    }

    /**
     * @param $item
     * @param $data
     * @param $totalWeight
     * @return void
     */
    private function processItem($item, &$data, $totalWeight)
    {
        $itemWeight = (float)$item->getWeight() != 0 ? $item->getWeight() : 0.3;
        $result = [
            "name" => $item->getName(),
            "weight" => $itemWeight * $item->getQtyOrdered(),
            "quantity" => (int)$item->getQtyOrdered(),
            "product_code" => "",
        ];

        if ($this->isTotalWeightGreaterThan20($totalWeight)) {
            $result['product_code'] = $item->getSku();
            $result['height'] = $item->getHeight() ?? null;
            $result['width'] = $item->getWidth() ?? null;
            $result['length'] = $item->getLength() ?? null;
        }

        $data[] = $result;
    }

    /**
     * @param $totalWeight
     * @return bool
     */
    private function isTotalWeightGreaterThan20($totalWeight)
    {
        return $totalWeight > 20;
    }

    /**
     * @param Order $order
     * @param \Magento\Inventory\Model\Source $sourceItem
     * @param $token
     *
     * @return array|mixed
     */
    protected function getXfastAvai($order, $sourceItem, $token)
    {
        if (!$this->getConfigData(self::KEY_XFAST_SERVICE)) {
            return [];
        }
        $shippingAddress = $order->getShippingAddress();
        $street = $shippingAddress->getStreet();
        if (is_array($street)) {
            $street = implode(', ', $street);
        }
        $payload = [
            'token' => $token,
            'pick_street' => $sourceItem->getStreet(),
            'pick_ward' => $sourceItem->getWard(),
            'pick_district' => $sourceItem->getDistrict(),
            'pick_province' => $sourceItem->getCity(),
            'customer_province' => $shippingAddress->getCity(),
            'customer_district' => $shippingAddress->getDistrict(),
            'customer_ward' => $shippingAddress->getWard(),
            'customer_street' => $street,
            'customer_first_address' => $street,
        ];
        $uri = $this->getModeUrl() . '/services/shipment/x-team';
        $this->_logger->debug('Check Xfast Request: ' . $this->serializer->serialize($payload));
        $response = $this->serializer->unserialize($this->apiCall('GET', $payload, $uri));
        $this->_logger->debug('Check Xfast Response: ' . $this->serializer->serialize($response));
        if (isset($response['success'])) {
            if ($response['success'] && isset($response['data']) && !empty($response['data'])) {
                return $response['data'];
            } elseif (!$response['success'] && isset($response['message'])) {
                $this->messageManager->addNoticeMessage(__($response['message']));
            }
        }

        return [];
    }
}
