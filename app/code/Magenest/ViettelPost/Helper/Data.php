<?php
namespace Magenest\ViettelPost\Helper;

use Magenest\ViettelPost\Model\ResourceModel\Province\CollectionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_resource;
    protected $_encryptor;
    protected $addressRenderer;
    protected $_cacheInterface;
    protected $provinceCollectionFactory;
    protected $districtCollectionFactory;
    protected $wardsCollectionFactory;
    protected $token;
    protected $_requestInterface;
    private $backendShippingMethod = [];
    private $frontendShippingMethod = [];

    public function __construct(
        RequestInterface $requestInterface,
        \Magenest\ViettelPost\Model\ResourceModel\Province\CollectionFactory $provinceCollectionFactory,
        \Magenest\ViettelPost\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        \Magenest\ViettelPost\Model\ResourceModel\Wards\CollectionFactory $wardsCollectionFactory,
        \Magenest\ViettelPost\Logger\Logger $logger,
        CacheInterface $cache,
        EncryptorInterface $encryptor,
        ResourceConnection $resourceConnection,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        Context $context,
        array $frontendShippingMethod = [],
        array $backendShippingMethod = []
    ) {
        parent::__construct($context);
        $this->_requestInterface = $requestInterface;
        $this->_resource = $resourceConnection;
        $this->_logger = $logger;
        $this->_encryptor = $encryptor;
        $this->_cacheInterface = $cache;
        $this->addressRenderer = $addressRenderer;
        $this->provinceCollectionFactory = $provinceCollectionFactory;
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->wardsCollectionFactory = $wardsCollectionFactory;
        $this->frontendShippingMethod = array_keys($frontendShippingMethod);
        $this->backendShippingMethod = $backendShippingMethod;
    }

    public function getCarrierModel($carrierCode)
    {
        if (isset($this->backendShippingMethod[$carrierCode])) {
            $carrierModelType = $this->backendShippingMethod[$carrierCode];
            $carrierModel = ObjectManager::getInstance()->get($carrierModelType);
            return $carrierModel;
        } else {
            return false;
        }
    }

    public function getBackendCarrierList()
    {
        return $this->backendShippingMethod;
    }

    public function isUsedBackendShipping($order)
    {
        $shippingMethod = $order->getShippingMethod();
        return in_array($shippingMethod, $this->frontendShippingMethod);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function isCOD($order)
    {
        return ($order->getPayment()->getMethod() == 'cashondelivery');
    }

    const PROVINCE_URL = 'https://partner.viettelpost.vn/v2/categories/listProvinceById?provinceId=';
    const DISTRICT_URL = 'https://partner.viettelpost.vn/v2/categories/listDistrict?provinceId=';
    const WARDS_URL = 'https://partner.viettelpost.vn/v2/categories/listWards?districtId=';
    const LOGIN_API_URL = 'https://partner.viettelpost.vn/v2/user/Login';
    const CREATE_ORDER_API_URL = 'https://partner.viettelpost.vn/v2/order/createOrder';
    const PRINT_SHIPPING_LABEL_URL = 'https://partner.viettelpost.vn/v2/order/encryptLinkPrint';
    const TOKEN_CACHE_ID = 'VIETTELPOST_TOKEN';

    public function getProvinceUrlApi($provinceId = '-1')
    {
        return self::PROVINCE_URL.$provinceId;
    }

    public function getDistrictUrlApi($provinceId = '-1')
    {
        return self::DISTRICT_URL.$provinceId;
    }

    public function getWardsUrlApi($districtId = '-1')
    {
        return self::WARDS_URL.$districtId;
    }

    public function sendRequest($url, $method, $rawData = null, $token = null)
    {
        $this->_logger->debug($method.":".$url);
        $client = new \Zend\Http\Client();
        $options = [
//            'adapter' => 'Zend\Http\Client\Adapter\Curl',
//            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
//            'maxredirects' => 0,
            'timeout' => 15
        ];
        $client->setOptions($options);
        if ($rawData) {
            $this->_logger->debug($rawData);
            $client->setRawBody($rawData);
        }
        $client->setUri($url);
        $client->setMethod(strtoupper($method));
        $header = [];
        $header['Content-Type'] = 'application/json';
        if ($token) {
            $header['Token'] = $token;
        }
        $client->setHeaders($header);
        try {
            $response = $client->send();
            $dataResp = [
                'success' => true,
                'body' => $response->getBody(),
                'status_code' => $response->getStatusCode()
            ];
            $this->_logger->debug(var_export($dataResp, true));
            return $dataResp;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'exception' => $e
            ];
        }
    }

    public function login($reLogin = false)
    {
        $token = $this->_cacheInterface->load(self::TOKEN_CACHE_ID);
        if (!$token) {
            $reLogin = true;
        }
        if ($reLogin) {
            $data = [
                'USERNAME' => $this->getUsername(),
                'PASSWORD' => $this->getPassword()
            ];
            $resp = $this->sendRequest(self::LOGIN_API_URL, 'POST', json_encode($data));
            $body = json_decode($resp['body'], true);
            $newToken = isset($body['data']['token']) ? $body['data']['token'] : "";
            if ($newToken) {
                $token = $newToken;
                $this->_cacheInterface->save($token, self::TOKEN_CACHE_ID);
            }
        }
        $this->token = $token;
        return $token;
    }

    public function sendOrder($data)
    {
        try {
            $resp = $this->sendRequest(self::CREATE_ORDER_API_URL, 'POST', json_encode($data), $this->token);
            $respBody = isset($resp['body']) ? $resp['body'] : "";
            $respBody = json_decode($respBody, true);
            $status = isset($respBody['status']) ? $respBody['status'] : "";
            $message = isset($respBody['message']) ? $respBody['message'] : "";
            if ($status != 200) {
                throw new \Exception(__($message));
            }
        } catch (\Exception $e) {
            if (($status == 201) || ($status == 202)) {
                $this->login(true);
                $resp = $this->sendRequest(self::CREATE_ORDER_API_URL, 'POST', json_encode($data), $this->token);
                if ($status != 200) {
                    throw new \Exception(__($message));
                }
            } else {
                throw new \Exception(__($message));
            }
        }
        return $resp;
    }

    public function getAddressData($url)
    {
        $data = [];
        $dataResp = $this->sendRequest($url, 'get');
        if ($dataResp['success']) {
            $apiData = json_decode($dataResp['body'], true);
            $data = isset($apiData['data'])?$apiData['data']:[];
            array_walk($data, function (&$value) {
                $value = array_change_key_case($value, CASE_LOWER);
            });
        }
        return $data;
    }

    public function isShipmentValidate($order, $shipment)
    {
        if ($this->isUsedBackendShipping($order)) {
//            $shipmentParams = $this->_requestInterface->getParam('shipment');
//            $carrierSelect = isset($shipmentParams['carrier_select'])?$shipmentParams['carrier_select']:"";
            $carrierName = $shipment->getData(\Magenest\ViettelPost\Setup\UpgradeData::SHIPMENT_CARRIER_NAME);
            if (!$carrierName) {
                throw new LocalizedException(__('You must choose a shipping carrier'));
            }
        }
        return true;
    }

    public function isShipmentShouldCreate($order, $shipment)
    {
        $carrierName = $shipment->getData(\Magenest\ViettelPost\Setup\UpgradeData::SHIPMENT_CARRIER_NAME);
        return !$carrierName;
    }

    /**
     * Returns string with formatted address
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @return null|string
     */
    public function getFormattedAddress(\Magento\Sales\Model\Order\Address $address)
    {
        return $this->addressRenderer->format($address, 'oneline');
    }

    public function saveAddressInfoData($table, $data)
    {
        $this->_resource->getConnection()->insertOnDuplicate($this->_resource->getTableName($table), $data);
    }

    //https://partner.viettelpost.vn/?uId=cuoc-va-van-don
    public function getOrderService()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/order_service');
    }
    public function getSenderFullName()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/sender_fullname');
    }
    public function getSenderAddress()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/sender_address');
    }
    public function getSenderPhone()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/sender_phone');
    }
    public function getSenderEmail()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/sender_email');
    }
    public function getSenderProvince()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/sender_province')?:"0";
    }
    public function getSenderDistrict()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/sender_district')?:"0";
    }
    public function getSenderWards()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/sender_wards')?:"0";
    }

    public function getVTOrderPrefix()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/information/order_prefix')?:"";
    }

    public function getUsername()
    {
        return $this->scopeConfig->getValue('carriers/viettelpost/general/username');
    }

    public function getPassword()
    {
        return $this->_encryptor->decrypt($this->scopeConfig->getValue('carriers/viettelpost/general/password'));
    }

    public function getPrintToken()
    {
        return $this->_encryptor->decrypt($this->scopeConfig->getValue('carriers/viettelpost/general/token_print'));
    }

    public function getViettelCityId($cityName, $cityCode = null)
    {
        $provinceCol = $this->provinceCollectionFactory->create();
        if ($cityName) {
            $provinceCol->addFieldToFilter("province_name", [
                ['like' => '%'.$cityName.'%']
            ]);
        }
        if ($cityCode) {
            $provinceCol->addFieldToFilter("province_code", $cityCode);
        }
        $province = $provinceCol->getFirstItem();
        return $province->getProvinceId()?:"0";
    }

    public function getViettelDistrictId($provinceId, $districtName)
    {
        $districtCol = $this->districtCollectionFactory->create();
        if ($provinceId) {
            $districtCol->addFieldToFilter("province_id", $provinceId);
        }
        if ($districtName) {
            $districtCol->addFieldToFilter("district_name", [
                ['like' => '%'.$districtName.'%']
            ]);
        }
        $district = $districtCol->getFirstItem();
        return $district->getDistrictId()?:"0";
    }

    public function getViettelWardId($districtId, $wardName)
    {
        $wardCol = $this->wardsCollectionFactory->create();
        if ($districtId) {
            $wardCol->addFieldToFilter("district_id", $districtId);
        }
        if ($wardName) {
            $wardCol->addFieldToFilter("wards_name", [
                ['like' => '%'.$wardName.'%']
            ]);
        }
        $ward = $wardCol->getFirstItem();
        return $ward->getWardsId()?:"0";
    }
}
