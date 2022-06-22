<?php
namespace Magenest\API247\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\AsyncClient\Request;
use Magento\Framework\HTTP\AsyncClientInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Webapi\Response;

class API247Post
{
    const TOKEN_CACHE = "API247_TOKEN";
    const CLIENT_ID_CACHE = 'API247_CLIENT_ID';

    const USERNAME_CONFIG = "carriers/api247/username";
    const PASSWORD_CONFIG = "carriers/api247/password";
    const TEST_MODE = 'carriers/api247/test_mode';
    const PRODUCTION_API = 'carriers/api247/production_api';
    const SANDBOX_API = 'carriers/api247/sandbox_api';
    CONST FIXED_SERVICE_TYPES = 'carriers/api247/fixed_service_types';

    const LOGIN_ENDPOINT = '/api/Client/ClientLogin';
    const SERVICE_TYPES  = "/Api/Customer/GetServiceTypes";
    const CUSTOMER_SERVICE_LIST = "/api/Customer/Services";
    const CUSTOMER_GET_CLIENT_HUB = '/Api/Customer/CustomerGetClientHubs';
    const CUSTOMER_UPDATE_ORDER = "/api/Customer/CustomerAPIUpdateOrder";
    const CUSTOMER_CANCEL_ORDER = "/Api/Customer/CancelOrder";
    const CUSTOMER_GET_ORDER_IMAGES = "/Api/Customer/GetOrderImages";
    const CUSTOMER_GET_PRICE_FOR_CUSTOMER = '/Api/Customer/GetPriceForCustomerAPI';
    const CUSTOMER_CREATE_ORDER = "/Api/Customer/CustomerAPICreateOrder";
    const CUSTOMER_INSERT_CLIENT_HUB = "/Api/Customer/CustomerInsertClientHub";
    const TRACKING_ORDER = "https://tracking.247post.vn/api/Order/v1/Tracking";
    const WEB_HOOK = '/IntergrationAPI';

    /** @var bool */
    private $connected = false;

    /** @var AsyncClientInterface */
    private $asyncClient;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var SerializerInterface */
    private $serializer;

    /** @var CacheInterface */
    private $cache;

    /**
     * @param AsyncClientInterface $asyncClient
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param CacheInterface $cache
     *
     * @throws \Throwable
     */
    public function __construct(
        AsyncClientInterface $asyncClient,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        CacheInterface $cache
    ) {
        $this->asyncClient = $asyncClient;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->cache = $cache;
    }

    /**
     * Response parser for AsyncClientInterface (synchronously)
     *
     * Please make sure returned response is JSON (Content-Type = 'application/json') before using
     *
     * @param Request $request
     * @return array|bool|float|int|string|null
     *
     * @throws \Throwable
     */
    private function synchronousRequest(Request $request)
    {
        $response = $this->asyncClient->request($request)->get();
        try {
            $result = $this->serializer->unserialize($response->getBody());
        } catch (\Exception $exception) {
            $result = $this->serializer->serialize($response->getBody());
            throw new LocalizedException(__("API247: " . empty($result) ? $result : "Không có phản hồi"));
        }
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $message = $result['message'] ?? "Can't send request to API247 Post API. Please try again.";
            throw new LocalizedException(__("API247: " . $message));
        }

        return $result;
    }

    /**
     * CONNECT: POST@partner.viettelpost.vn/v2/user/ownerconnect
     *
     * @return $this
     * @throws \Throwable
     */
    public function connect()
    {
        if (!$this->connected) {
            $username = $this->scopeConfig->getValue(self::USERNAME_CONFIG);
            $password = $this->scopeConfig->getValue(self::PASSWORD_CONFIG);

            $token = $this->cache->load(self::TOKEN_CACHE);
            $clientID = $this->cache->load(self::CLIENT_ID_CACHE);
            if (!$token || !$clientID) {
                $this->login($username, $password);
            }
            $this->connected = true;
        }

        return $this;
    }

    /**
     * SIGN_IN: POST@partner.viettelpost.vn/v2/user/Login
     *
     * @param $username
     * @param $password
     *
     * @return string|void
     * @throws \Throwable
     */
    private function login($username, $password)
    {
        $body = $this->serializer->serialize([
            "USERNAME" => $username,
            "PASSWORD" => $password
        ]);
        $tokenRequest  = new Request(
            $this->getDomain() . self::LOGIN_ENDPOINT,
            Request::METHOD_POST,
            ['Content-Type' => 'application/json'],
            $body
        );

        $response = $this->synchronousRequest($tokenRequest);
        if (isset($response['Token']) && isset($response['ClientID'])) {
            $this->cache->save($response['Token'], self::TOKEN_CACHE, [], 86400);
            $this->cache->save($response['ClientID'], self::CLIENT_ID_CACHE, [], 86400);
            return $response;
        }
    }

    public function getCustomerServiceTypes()
    {
        if (!$this->cache->load(self::TOKEN_CACHE) && !$this->cache->load(self::CLIENT_ID_CACHE)) {
            $this->connect();
        }
        $response =  $this->synchronousRequest(new Request(
            $this->getDomain() . self::SERVICE_TYPES,
            Request::METHOD_POST,
            ['Content-Type' => 'application/json', 'Token' => $this->cache->load(self::TOKEN_CACHE), 'ClientID' => $this->cache->load(self::CLIENT_ID_CACHE)],
            "{}"));

        return $response["ServiceTypes"];
    }

    public function getCustomerServiceLists()
    {
        if (!$this->cache->load(self::TOKEN_CACHE) && !$this->cache->load(self::CLIENT_ID_CACHE)) {
            $this->connect();
        }
        $response =  $this->synchronousRequest(new Request(
            $this->getDomain() . self::CUSTOMER_SERVICE_LIST,
            Request::METHOD_POST,
            ['Content-Type' => 'application/json', 'Token' => $this->cache->load(self::TOKEN_CACHE), 'ClientID' => $this->cache->load(self::CLIENT_ID_CACHE)],
            "{}"));

        return $response["Services"];
    }

    public function getCustomerClientHub()
    {
        if (!$this->cache->load(self::TOKEN_CACHE) && !$this->cache->load(self::CLIENT_ID_CACHE)) {
            $this->connect();
        }
        $response =  $this->synchronousRequest(new Request(
            $this->getDomain() . self::CUSTOMER_GET_CLIENT_HUB,
            Request::METHOD_POST,
            ['Content-Type' => 'application/json', 'Token' => $this->cache->load(self::TOKEN_CACHE), 'ClientID' => $this->cache->load(self::CLIENT_ID_CACHE)],
            "{}")
        );

        return $response["Hubs"];
    }

    public function setCustomerClientHub($params)
    {
        if (!$this->cache->load(self::TOKEN_CACHE) && !$this->cache->load(self::CLIENT_ID_CACHE)) {
            $this->connect();
        }
        $response =  $this->synchronousRequest(new Request(
            $this->getDomain() . self::CUSTOMER_INSERT_CLIENT_HUB,
            Request::METHOD_POST,
            ['Content-Type' => 'application/json', 'Token' => $this->cache->load(self::TOKEN_CACHE), 'ClientID' => $this->cache->load(self::CLIENT_ID_CACHE)],
            $this->serializer->serialize($params)));

        return $response["HubInfo"];
    }

    /**
     * ALL_PRICE: POST@partner.viettelpost.vn/v2/order/getPriceAll
     *
     * @param $params
     * @return string
     * @throws \Throwable
     */
    public function getAllPrice($params)
    {
        $token = $this->cache->load(self::TOKEN_CACHE);
        $clientId = $this->cache->load(self::CLIENT_ID_CACHE);

        $response = $this->synchronousRequest(new Request(
                $this->getDomain() . self::CUSTOMER_GET_PRICE_FOR_CUSTOMER,
                Request::METHOD_POST,
                ['Content-Type' => 'application/json', 'Token' => $token, 'ClientID' => $clientId],
                $this->serializer->serialize($params))
        );

        return $response;
    }

    /**
     * CREATE_ORDER: POST@partner.viettelpost.vn/v2/order/createOrder
     *
     * @param $params
     * @return array|bool|float|int|string|null
     * @throws \Throwable
     */
    public function createOrder($params)
    {
        $token = $this->cache->load(self::TOKEN_CACHE);
        $clientId = $this->cache->load(self::CLIENT_ID_CACHE);

        $response = $this->synchronousRequest(new Request(
                $this->getDomain() . self::CUSTOMER_CREATE_ORDER,
                Request::METHOD_POST,
                ['Content-Type' => 'application/json', 'Token' => $token, 'ClientID' => $clientId],
                $this->serializer->serialize($params))
        );

        return $response;
    }

    public function getDomain()
    {
        if ($this->scopeConfig->getValue(self::TEST_MODE) == 1) {
            return $this->scopeConfig->getValue(self::SANDBOX_API);
        } else {
            return $this->scopeConfig->getValue(self::PRODUCTION_API);
        }
    }
}
