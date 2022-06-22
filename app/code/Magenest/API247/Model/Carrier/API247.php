<?php

namespace Magenest\API247\Model\Carrier;

use Magenest\Directory\Model\ResourceModel\City as DirectoryProvinceResource;
use Magenest\Directory\Model\ResourceModel\District as DirectoryDistrictResource;
use Magenest\Directory\Model\ResourceModel\Ward as DirectoryWardResource;
use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magenest\API247\Model\API247Post;
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

class API247 extends AbstractCarrierOnline implements CarrierInterface
{
    const CODE = 'api247';

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
    /** @var API247Post */
    private $API247Post;
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
     * @param DirectoryProvinceResource $directoryProvinceResource
     * @param DirectoryDistrictResource $directoryDistrictResource
     * @param DirectoryWardResource $directoryWardResource
     * @param SerializerInterface $serializer
     * @param OrderRepositoryInterface $orderRepository
     * @param SourceRepositoryInterface $sourceRepository
     * @param CacheInterface $cache
     * @param API247Post $API247Post
     * @param ManagerInterface $messageManager
     * @param NotifierInterface $notifier
     * @param \Magenest\CustomSource\Helper\Data $areaData
     * @param \Magento\Inventory\Model\ResourceModel\Source $inventoryResource
     * @param SourceFactory $sourceFactory
     * @param Registry $registry
     * @param array $data
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
        DirectoryProvinceResource                            $directoryProvinceResource,
        DirectoryDistrictResource                            $directoryDistrictResource,
        DirectoryWardResource                                $directoryWardResource,
        SerializerInterface                                  $serializer,
        OrderRepositoryInterface                             $orderRepository,
        SourceRepositoryInterface                            $sourceRepository,
        CacheInterface                                       $cache,
        API247Post                                           $API247Post,
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
        $this->directoryProvinceResource = $directoryProvinceResource;
        $this->directoryDistrictResource = $directoryDistrictResource;
        $this->directoryWardResource = $directoryWardResource;
        $this->API247Post = $API247Post;
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

                    $API247 = $this->API247Post->connect();
                    $getClientHubId = $API247->getCustomerClientHub();

                    $requestRates = new DataObject([
                        'ToProvinceName' => $sourceItem->getCity(),
                        'ToDistrictName' => $sourceItem->getDistrict(),
                        'ToWardName' => $sourceItem->getWard(),
                        'Length' => $request->getPackageDepth(),
                        'Width' => $request->getPackageWidth(),
                        'Height' => $request->getPackageHeight(),
                        'RealWeight' => $request->getPackageWeight(),
                        'ClientHubID' => $getClientHubId[0]['ClientHubID'],
                        'ServiceTypeID' => $this->_scopeConfig->getValue(API247Post::FIXED_SERVICE_TYPES) ?? "DE",
                    ]);



                    $shippingPriceArray = $this->getRates($requestRates);
                    if ($shippingPriceArray['success']) {
                        $shippingPrice = $shippingPriceArray['TotalServiceCost'];
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

        $params = [
            "ToProvinceName" => $request->getData('ToProvinceName'),
            "ToDistrictName" => $request->getData('ToDistrictName'),
            "ToWardName"    => $request->getData('ToWardName'),
            "Length"        => (int)$request->getData('Length'),
            "Width"         => (int)$request->getData('Width'),
            "Height"        => (int)$request->getData('Height'),
            "RealWeight"    => (float)$request->getData('RealWeight'),
            "ClientHubID"   => $request->getData('ClientHubID'),
            "ServiceTypeID" => $request->getData('ServiceTypeID'),
        ];

        $API247 = $this->API247Post->connect();
        $this->_logger->debug("API247 PRICE REQUEST", ['request' => $this->serializer->serialize($params)]);
        $response = $API247->getAllPrice($params);
        $this->_logger->debug("API247 PRICE RESPONSE", ['response' => $this->serializer->serialize($response)]);

        $result = $response;
        $result['success'] = true;
        return $result;
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
        $result->setShippingLabelContent('Api247 Post');
        $packagesWeight = 0;
        if ($request->getOrderShipment() && !$request->getOrderShipment()->getOrder()->getApiOrderId()) {
            $packages = $request->getPackages();
            foreach ($packages as $package) {
                $packagesWeight += (int)$package['params']['weight'];
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
            $API247 = $this->API247Post->connect();
            $this->_logger->debug("API247 ORDER REQUEST", ['request' => $this->serializer->serialize($params)]);
            $response = $API247->createOrder($params);
            $this->_logger->debug(
                "API247 ORDER RESPONSE",
                ['response' => $this->serializer->serialize($response)]
            );

            if (isset($response['Errors']) && !$response['Errors']) {
                $shippingFee = $response['OrderInfo']['TotalServiceCost'];
                $orderCode = $response['OrderInfo']['OrderCode'];
                $order->setData('api_order_id', $orderCode);
                $order->setData('shipping_fee', $shippingFee);
                $order->setState(Order::STATE_PROCESSING);
                $order->setStatus(UpdateOrderStatus::PROCESSING_SHIPMENT_STATUS);
                $this->orderRepository->save($order);
                $result->setShippingApiId($orderCode);
                $this->messageManager->addSuccessMessage("API247 Order {$orderCode} created successfully.");
            }
        } catch (Throwable $e) {
            $this->_logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->messageManager->addErrorMessage("Errors happened while creating order: " . $e->getMessage());
            $this->notifier->addCritical(
                __("API247 Order"),
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
        $API247 = $this->API247Post->connect();
        $getClientHubs = $API247->getCustomerClientHub();
        $telephone = $order->getShippingAddress()->getTelephone();
        if ($telephone[0] != "0") {
            $telephone = "0" . $telephone;
        }
        $clientTelephone = $sourceItem->getPhone();
        if ($clientTelephone[0] != "0") {
            $clientTelephone = "0" . $clientTelephone;
        }

        $data = [];
        $itemData = [];
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
            $itemData[] = [
              'ItemID'   => $item->getItemId(),
              'ItemName' => $item->getName(),
              'Qty'      => (int)$item->getQtyOrdered(),
              'UnitPrice' => (int)$item->getPrice(),
              'Amount'   => (int)$item->getPrice() * (int)$item->getQtyOrdered()
            ];
        }

        $province = $order->getShippingAddress()->getCity();
        $district = $order->getShippingAddress()->getDistrict();
        $street = $order->getShippingAddress()->getStreet()[0];
        $ward = $order->getShippingAddress()->getWard();
        $pickMoney = (int)$order->getGrandTotal();
        return [
            "OrderInfo" => [
                "ClientHubID" => $getClientHubs[0]['ClientHubID'],
                "SenderPhone" => $telephone,
                "SenderName" => $order->getShippingAddress()->getName(),
                "SenderProvinceName" => $province,
                "SenderDistrictName" => $district,
                "SenderWardName" => $ward,
                "SenderDetailAddress" => $street,
                "SenderAddress" => $street . "," . $ward . "," . $district . "," . $province,
                "CustomerPrice" => $order->getPayment()->getMethod() == "cashondelivery" ? (int)$pickMoney : 0,
                "ReceiverPhone" => $clientTelephone,
                "ReceiverName" => $sourceItem->getName() ? $sourceItem->getName() : "Kangaroo",
                "ReceiverAddress" => $sourceItem->getStreet() . "," . $sourceItem->getWard() . "," . $sourceItem->getDistrict() . "," . $sourceItem->getCity(),
                "ReceiverProvinceName" => $sourceItem->getCity(),
                "ReceiverDistrictName" => $sourceItem->getDistrict(),
                "ReceiverWardName" => $sourceItem->getWard(),
                "RealWeight" => (int)$weight,
                "Quantity" => (int)$order->getTotalQtyOrdered(),
                "ServiceTypeID" => $this->_scopeConfig->getValue(API247Post::FIXED_SERVICE_TYPES) ?? "DE",
                "InformFee" => (int)$pickMoney,
                "Items" => $itemData
            ]
        ];
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
}
