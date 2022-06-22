<?php

namespace Magenest\ViettelPostCarrier\Model\Carrier;

use Magenest\Directory\Model\ResourceModel\City as DirectoryProvinceResource;
use Magenest\Directory\Model\ResourceModel\District as DirectoryDistrictResource;
use Magenest\Directory\Model\ResourceModel\Ward as DirectoryWardResource;
use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magenest\ViettelPost\Model\ResourceModel\District;
use Magenest\ViettelPost\Model\ResourceModel\Province;
use Magenest\ViettelPost\Model\ResourceModel\Wards;
use Magenest\ViettelPostCarrier\Model\ViettelPostApi;
use Magenest\ViettelPostCarrier\Plugin\Model\TotalsInformationManagement;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Checkout\Model\Session;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Xml\Security;
use Magento\Inventory\Model\Source;
use Magento\Inventory\Model\SourceFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Simplexml\ElementFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Psr\Log\LoggerInterface;
use Throwable;

class ViettelPost extends AbstractCarrierOnline implements CarrierInterface
{
    const CODE = 'viettelPostCarrier';
    const SUPPORTED_DISTRICTS_CODE = [
        '121',
        '122',
        '123',
        '124',
        '125',
        '126',
        '127',
        '128',
        '129',
        '133',
        '135',
        '137',
        '658',
        '667',
        '668',
        '669',
        '670',
        '671',
        '672',
        '673',
        '674',
        '676',
        '663',
        '662',
        '664',
        '666',
        '665',
        '675'
    ];
    const OPTION_SHIPMENT = 'option_shipment';

    /** @var string */
    protected $_code = self::CODE;

    /** @var bool */
    protected $_isFixed = true;
    /** @var SourceFactory */
    protected $sourceFactory;
    /** @var \Magento\Inventory\Model\ResourceModel\Source */
    protected $inventoryResource;
    /** @var Session */
    private $checkoutSession;
    /** @var Province */
    private $provinceResource;
    /** @var District */
    private $districtResource;
    /** @var Wards */
    private $wardsResource;
    /** @var ViettelPostApi */
    private $viettelPostApi;
    /** @var DirectoryProvinceResource */
    private $directoryProvinceResource;
    /** @var DirectoryDistrictResource */
    private $directoryDistrictResource;
    /** @var DirectoryWardResource */
    private $directoryWardResource;
    /** @var CacheInterface */
    private $cache;
    /** @var SerializerInterface */
    private $serializer;
    /** @var SourceRepositoryInterface */
    private $sourceRepository;
    /** @var OrderRepositoryInterface */
    private $orderRepository;
    /** @var NotifierInterface */
    private $notifier;
    /** @var ManagerInterface */
    private $messageManager;
    /**
     * @var \Magenest\CustomSource\Helper\Data
     */
    private $areaData;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param ElementFactory $xmlElFactory
     * @param ResultFactory $rateFactory
     * @param MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param StatusFactory $trackStatusFactory
     * @param RegionFactory $regionFactory
     * @param CountryFactory $countryFactory
     * @param CurrencyFactory $currencyFactory
     * @param Data $directoryData
     * @param StockRegistryInterface $stockRegistry
     * @param Session $checkoutSession
     * @param Province $provinceResource
     * @param District $districtResource
     * @param Wards $wardsResource
     * @param DirectoryProvinceResource $directoryProvinceResource
     * @param DirectoryDistrictResource $directoryDistrictResource
     * @param DirectoryWardResource $directoryWardResource
     * @param SerializerInterface $serializer
     * @param OrderRepositoryInterface $orderRepository
     * @param SourceRepositoryInterface $sourceRepository
     * @param CacheInterface $cache
     * @param ViettelPostApi $viettelPostApi
     * @param ManagerInterface $messageManager
     * @param NotifierInterface $notifier
     * @param \Magenest\CustomSource\Helper\Data $areaData
     * @param \Magento\Inventory\Model\ResourceModel\Source $inventoryResource
     * @param SourceFactory $sourceFactory
     * @param array $data
     *
     */
    public function __construct(
        ScopeConfigInterface                                 $scopeConfig,
        ErrorFactory                                         $rateErrorFactory,
        LoggerInterface                                      $logger,
        Security                                             $xmlSecurity,
        ElementFactory                                       $xmlElFactory,
        ResultFactory                                        $rateFactory,
        MethodFactory                                        $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory       $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        StatusFactory                                        $trackStatusFactory,
        RegionFactory                                        $regionFactory,
        CountryFactory                                       $countryFactory,
        CurrencyFactory                                      $currencyFactory,
        Data                                                 $directoryData,
        StockRegistryInterface                               $stockRegistry,
        Session                                              $checkoutSession,
        Province                                             $provinceResource,
        District                                             $districtResource,
        Wards                                                $wardsResource,
        DirectoryProvinceResource                            $directoryProvinceResource,
        DirectoryDistrictResource                            $directoryDistrictResource,
        DirectoryWardResource                                $directoryWardResource,
        SerializerInterface                                  $serializer,
        OrderRepositoryInterface                             $orderRepository,
        SourceRepositoryInterface                            $sourceRepository,
        CacheInterface                                       $cache,
        ViettelPostApi                                       $viettelPostApi,
        ManagerInterface                                     $messageManager,
        NotifierInterface                                    $notifier,
        \Magenest\CustomSource\Helper\Data                   $areaData,
        \Magento\Inventory\Model\ResourceModel\Source        $inventoryResource,
        SourceFactory                                        $sourceFactory,
        Registry $registry,
        array                                                $data = []
    ) {
        $this->cache = $cache;
        $this->notifier = $notifier;
        $this->serializer = $serializer;
        $this->messageManager = $messageManager;
        $this->orderRepository = $orderRepository;
        $this->sourceRepository = $sourceRepository;
        $this->checkoutSession = $checkoutSession;
        $this->provinceResource = $provinceResource;
        $this->districtResource = $districtResource;
        $this->wardsResource = $wardsResource;
        $this->directoryProvinceResource = $directoryProvinceResource;
        $this->directoryDistrictResource = $directoryDistrictResource;
        $this->directoryWardResource = $directoryWardResource;
        $this->viettelPostApi = $viettelPostApi;
        $this->areaData = $areaData;
        $this->inventoryResource = $inventoryResource;
        $this->sourceFactory = $sourceFactory;
        $this->_coreRegistry = $registry;
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
     * @param DataObject $request
     *
     * @return bool
     */
    public function processAdditionalValidation(DataObject $request)
    {
        return $this->getConfigFlag('username') && $this->getConfigFlag('password');
    }

    /**
     * @return bool
     */
    public function isShippingLabelsAvailable()
    {
        return true;
    }

    /**
     * Copy of \Magenest\GiaoHangTietKiem\Model\Carrier\GiaoHangTietKiem:collectRates
     * Not reflect the real shipping price
     *
     * @param RateRequest $request
     *
     * @return bool|Result|null
     */
    public function collectRates(RateRequest $request)
    {
        try {
            if (!$this->getConfigFlag('active')) {
                return false;
            }

            $freeBoxes = $this->getFreeBoxesCount($request);

            $result = $this->_rateFactory->create();
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            $shippingPrice = null;

            if ($request->getPackageQty() == $freeBoxes || $request->getFreeShipping()) {
                $shippingPrice = '0.00';
            } else {
                if ($request->getPackageValue() !== false) {
                    $district = $request->getDestDistrict();
                    if (is_array($district)) {
                        $district = $district[1] ?? $district;
                    }

                    $areaCode = $this->areaData->getCurrentArea();
                    $sourceItem = $this->sourceFactory->create();
                    if ($areaCode === 'mien_bac') {
                        $this->inventoryResource->load($sourceItem, 'hy', 'source_code');
                    } else {
                        $this->inventoryResource->load($sourceItem, $areaCode, 'area_code');
                    }

                    $requestRates = new DataObject([
                        'source_city' => $sourceItem->getCity(),
                        'source_city_id' => $sourceItem->getCityId(),
                        'source_district' => $sourceItem->getDistrict(),
                        'source_district_id' => $sourceItem->getDistrictId(),
                        'source_ward' => $sourceItem->getWard(),
                        'dest_city' => $request->getDestCity(),
                        'dest_city_id' => $request->getDestCityId(),
                        'dest_district' => $district,
                        'dest_district_id' => $request->getDestDistrictId(),
                        'weight' => $request->getPackageWeight(),
                        'package_value' => $request->getPackageValue(),
                        'cod' => 0
                    ]);

                    $shippingPriceArray = $this->getRates($requestRates);
                    if ($shippingPriceArray['success']) {
                        unset($shippingPriceArray['success']);
                        $minPrice = INF;
                        foreach ($shippingPriceArray as $item) {
                            if ($item['GIA_CUOC'] < $minPrice) {
                                $minPrice = $item['GIA_CUOC'];
                            }
                        }

                        if ($minPrice !== INF) {
                            $shippingPrice = $minPrice;
                        }
                    }
                }
            }

            if ($shippingPrice) {
                $method->setPrice($shippingPrice);
                $method->setCost($shippingPrice);
                $this->checkoutSession->setActiveShippingMethod($this->_code);
                $result->append($method);
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return $result;
    }

    /**
     * @param $request
     *
     * @return array|bool|float|int|string|null
     * @throws Throwable
     */
    public function getRates($request)
    {
        //        $cacheKey = "VTP-{$request->getSourceCityId()}-" . "{$request->getSourceDistrictId()}-"
        //            . "{$request->getDestCityId()}-" . "{$request->getDestDistrictId()}-"
        //            . "{$request->getWeight()}-" . "{$request->getPackageValue()}";
        //
        //        if (!$response = $this->cache->load($cacheKey)) {
        $senderCity = $this->convertCityIdToViettelPost($request->getSourceCityId());
        $senderDistrict = $this->convertDistrictIdToViettelPost($request->getSourceDistrictId(), $senderCity);
        $receiverCity = $this->convertCityIdToViettelPost($request->getDestCityId());
        $receiverDistrict = $this->convertDistrictIdToViettelPost($request->getDestDistrictId(), $receiverCity);

        if ($senderCity === false || $senderDistrict === false
            || $receiverCity === false
            || $receiverDistrict === false) {
            throw new LocalizedException(
                __("Address code not found. Please update Address data in ViettelPost Carriers configuration.")
            );
        }

        $params = [
            "SENDER_PROVINCE" => $senderCity,
            "SENDER_DISTRICT" => $senderDistrict,
            "RECEIVER_PROVINCE" => $receiverCity,
            "RECEIVER_DISTRICT" => $receiverDistrict,
            "PRODUCT_TYPE" => "HH",
            "PRODUCT_WEIGHT" => (int)$request->getWeight() * 1000,
            "PRODUCT_PRICE" => (int)$request->getPackageValue(),
            "TYPE" => 1
        ];

        if ($request['cod'] === true) {
            $params['MONEY_COLLECTION'] = (int)$request->getPackageValue();
        }

        $viettelPostApi = $this->viettelPostApi->connect();
        $this->_logger->debug("VIETTELPOST PRICE REQUEST", ['request' => $this->serializer->serialize($params)]);
        $response = $viettelPostApi->getAllPrice($params);
        $this->_logger->debug("VIETTELPOST PRICE RESPONSE", ['response' => $response]);
        //            $this->cache->save($response, $cacheKey, [], 604800);
        //        }

        $result = $this->serializer->unserialize($response);
        $result['success'] = true;
        return $result;
    }

    /**
     * @param $id
     *
     * @return string
     * @throws LocalizedException
     */
    private function convertCityIdToViettelPost($id)
    {
        return $this->provinceResource->searchProvinceCode($this->directoryProvinceResource->getShortNameById($id));
    }

    /**
     * @param $id
     * @param $cityId
     *
     * @return string
     * @throws LocalizedException
     */
    private function convertDistrictIdToViettelPost($id, $cityId)
    {
        return $this->districtResource->searchDistrictCode(
            $this->directoryDistrictResource->getShortNameById($id),
            $cityId
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
     * @param DataObject $request
     *
     * @return DataObject
     */
    protected function _doShipmentRequest(DataObject $request)
    {
        $result = new DataObject();
        $result->setTrackingNumber($request->getOrderShipment()->getOrderId());
        $result->setShippingLabelContent('Viettel Post');
        $packagesWeight = 0;
        if ($request->getOrderShipment() && !$request->getOrderShipment()->getOrder()->getApiOrderId()) {
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
     */
    public function createOrder($request, $result, $packagesWeight)
    {
        try {
            /** @var Order $order */
            $order = $request->getOrderShipment()->getOrder();
            $sourceItem = $this->sourceRepository->get(
                $order->getSourceCode() == "hn" ? "hy" : $order->getSourceCode()
            );

            $params = $this->getDeliveryParams($order, $sourceItem, $packagesWeight);
            $viettelPostApi = $this->viettelPostApi->connect();
            $this->_logger->debug("VIETTELPOST ORDER REQUEST", ['request' => $this->serializer->serialize($params)]);
            $response = $viettelPostApi->createOrder($params);
            $this->_logger->debug(
                "VIETTELPOST ORDER RESPONSE",
                ['response' => $this->serializer->serialize($response)]
            );

            if (isset($response['error']) && $response['error'] == false) {
                $shippingFee = $response['data']['MONEY_TOTAL'];
                $orderCode = $response['data']['ORDER_NUMBER'];
                $order->setData('api_order_id', $orderCode);
                $order->setData('shipping_fee', $shippingFee);
                $order->setState(Order::STATE_PROCESSING);
                $order->setStatus(UpdateOrderStatus::PROCESSING_SHIPMENT_STATUS);
                $this->orderRepository->save($order);
                $result->setShippingApiId($orderCode);
                $this->messageManager->addSuccessMessage("ViettelPost Order {$orderCode} created successfully.");
            }
        } catch (Throwable $e) {
            $this->_logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->messageManager->addErrorMessage("Errors happened while creating order: " . $e->getMessage());
            $this->notifier->addCritical(
                __("ViettelPost Order"),
                __("Errors happened while creating order: %1", $e->getMessage())
            );
        }
    }

    /**
     * @param Order $order
     * @param Source $sourceItem
     *
     * @return array
     * @throws LocalizedException
     */
    public function getDeliveryParams($order, $sourceItem, $packagesWeight)
    {
        if (empty($order->getShippingOption())) {
            throw new LocalizedException(__("Missing ViettelPost shipping method for this order."));
        }

        $shippingAddress = $order->getShippingAddress();

        // prepare contact infos
        $senderCity = $this->convertCityIdToViettelPost($sourceItem->getCityId());
        $senderDistrict = $this->convertDistrictIdToViettelPost($sourceItem->getDistrictId(), $senderCity);
        $senderWard = $this->convertWardIdToViettelPost($sourceItem->getWardId(), $senderDistrict);
        $senderPhone = $sourceItem->getPhone();

        $receiverAddresses = $shippingAddress->getStreet();
        $receiverCity = $this->convertCityIdToViettelPost($shippingAddress->getCityId());
        $receiverDistrict = $this->convertDistrictIdToViettelPost($shippingAddress->getDistrictId(), $receiverCity);
        $receiverWard = $this->convertWardIdToViettelPost($shippingAddress->getWardId(), $receiverDistrict);
        $receiverPhone = $shippingAddress->getTelephone();

        // Prepare products data
        $weight = 0;
        foreach ($order->getItems() as $item) {
            if (empty($item->getChildrenItems()) && $item->getParentItemId() == null) {
                $this->processItem($item, $products, $weight);
            } else {
                foreach ($item->getChildrenItems() as $child) {
                    $this->processItem($child, $products, $weight);
                }
            }
        }
        $firstProduct = reset($products);

        $cod = $order->getPayment()->getMethod() == Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE;

        return [
            "ORDER_NUMBER" => (string)$order->getIncrementId(),
            "GROUPADDRESS_ID" => (int)$order->getStoreId(),
            "CUS_ID" => (int)$order->getCustomerId(),
            "SENDER_FULLNAME" => $sourceItem->getName(),
            "SENDER_ADDRESS" => $sourceItem->getStreet(),
            "SENDER_PHONE" => (isset($senderPhone[0]) && $senderPhone[0] != "0") ? "0$senderPhone" : $senderPhone,
            "SENDER_EMAIL" => $sourceItem->getEmail(),
            "SENDER_WARD" => (int)$senderWard,
            "SENDER_DISTRICT" => (int)$senderDistrict,
            "SENDER_PROVINCE" => (int)$senderCity,
            "RECEIVER_FULLNAME" => $shippingAddress->getName(),
            "RECEIVER_ADDRESS" => reset($receiverAddresses),
            "RECEIVER_PHONE" => $receiverPhone[0] != "0" ? "0$receiverPhone" : $receiverPhone,
            "RECEIVER_EMAIL" => $shippingAddress->getEmail(),
            "RECEIVER_WARD" => (int)$receiverWard,
            "RECEIVER_DISTRICT" => (int)$receiverDistrict,
            "RECEIVER_PROVINCE" => (int)$receiverCity,
            "PRODUCT_NAME" => $firstProduct["PRODUCT_NAME"] . "...",
            "PRODUCT_PRICE" => (int)$order->getSubtotal(),
            "PRODUCT_WEIGHT" => empty($weight) ? (int)$packagesWeight : (int)$weight,
            "PRODUCT_QUANTITY" => (int)$order->getTotalQtyOrdered(),
            "PRODUCT_TYPE" => "HH",
            "ORDER_PAYMENT" => $cod ? 3 : 1,
            "MONEY_COLLECTION" => $cod ? (int)$order->getGrandTotal() : 0,
            "ORDER_SERVICE" => $order->getShippingOption() ?? "LCOD",
            "ORDER_VOUCHER" => "",
            "ORDER_NOTE" => str_replace(array("\r", "\n"), '', $order->getCustomerNote()),
            "LIST_ITEM" => $products
        ];
    }

    /**
     * @param $id
     * @param $districtId
     *
     * @return string
     * @throws LocalizedException
     */
    private function convertWardIdToViettelPost($id, $districtId)
    {
        return $this->wardsResource->searchWardCode($this->directoryWardResource->getShortNameById($id), $districtId);
    }

    /**
     * @param $item
     * @param $data
     * @param $weight
     *
     * @return void
     */
    private function processItem($item, &$data, &$weight)
    {
        $itemWeight = (float)$item->getWeight() != 0 ? $item->getWeight() : 0.3;
        $data[] = [
            "PRODUCT_NAME" => $item->getName(),
            "PRODUCT_PRICE" => $item->getPrice(),
            "PRODUCT_WEIGHT" => $itemWeight * $item->getQtyOrdered() * 1000,
            "PRODUCT_QUANTITY" => (int)$item->getQtyOrdered()
        ];

        $weight += ($itemWeight * $item->getQtyOrdered() * 1000);
    }
}
