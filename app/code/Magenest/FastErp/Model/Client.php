<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 * Created by PhpStorm.
 * User: crist
 * Date: 12/11/2021
 * Time: 14:32
 */

namespace Magenest\FastErp\Model;

use Magenest\FastErp\Model\ResourceModel\ErpHistoryLog as ErpHistoryLogResourceModel;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class Client
{
    const REGEX_PATH_PARAMETERS = '/{warehouseId}/';

    const FASTERP_ENABLED = 'fast_erp/credentials/enabled';

    const FASTERP_BASE_URL = 'fast_erp/credentials/base_url';

    const FASTERP_TOKEN_ENDPOINT = 'fast_erp/credentials/token_endpoint';

    const FASTERP_ORDER_ENDPOINT = 'fast_erp/credentials/orders_endpoint';

    const FASTERP_PRODUCT_ENDPOINT = 'fast_erp/credentials/products_endpoint';

    const FASTERP_STOCK_ENDPOINT = 'fast_erp/credentials/stock_endpoint';

    const FASTERP_WAREHOUSES_ENDPOINT = 'fast_erp/credentials/warehouses_endpoint';

    const FASTERP_CLIENT_ID = 'fast_erp/credentials/client_id';

    const FASTERP_CLIENT_SECRET = 'fast_erp/credentials/client_secret';

    /**
     * @var string
     */
    protected $redirect_uri_path;

    /**
     * @var string
     */
    protected $path_enalbed;

    /**
     * @var string
     */
    protected $path_client_id;

    /**
     * @var string
     */
    protected $path_client_secret;

    /**
     * @var array
     */
    protected $scope = [];

    /**
     * @var string
     */
    protected $_config;

    /**
     * @var ZendClientFactory
     */
    protected $_httpClientFactory;

    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var
     */
    protected $_helperData;

    /**
     * @var null|string
     */
    protected $clientId = null;

    /**
     * @var null|string
     */
    protected $clientSecret = null;

    /**
     * @var null|string
     */
    protected $redirectUri = null;

    /**
     * @var null
     */
    protected $state = null;

    /**
     * @var
     */
    protected $token;

    /**
     * @var LoggerInterface
     */

    protected $logger;

    /**
     * @var ErpHistoryLogResourceModel
     */
    protected $erpHistoryLogResourceModel;

    /**
     * @var ErpHistoryLogFactory
     */
    protected $erpHistoryLogFactory;

    /**
     * AbstractClient constructor.
     *
     * @param ZendClientFactory $httpClientFactory
     * @param ConfigInterface $config
     * @param UrlInterface $url
     * @param ErpHistoryLogResourceModel $erpHistoryLogResourceModel
     * @param \Magenest\FastErp\Model\ErpHistoryLogFactory $erpHistoryLogFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ZendClientFactory          $httpClientFactory,
        ConfigInterface            $config,
        UrlInterface               $url,
        ErpHistoryLogResourceModel $erpHistoryLogResourceModel,
        ErpHistoryLogFactory       $erpHistoryLogFactory,
        LoggerInterface            $logger
    ) {
        $this->_httpClientFactory         = $httpClientFactory;
        $this->_config                    = $config;
        $this->_url                       = $url;
        $this->redirectUri                = $this->_url->getBaseUrl() . $this->redirect_uri_path;
        $this->clientId                   = $this->_getClientId();
        $this->clientSecret               = $this->_getClientSecret();
        $this->_config                    = $config;
        $this->erpHistoryLogResourceModel = $erpHistoryLogResourceModel;
        $this->erpHistoryLogFactory       = $erpHistoryLogFactory;
        $this->logger                     = $logger;
    }

    /**
     * @return string
     */
    protected function _getClientSecret()
    {
        return $this->_getStoreConfig(self::FASTERP_CLIENT_SECRET);
    }

    /**
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param $xmlPath
     *
     * @return mixed
     */
    protected function _getStoreConfig($xmlPath)
    {
        return $this->_config->getValue($xmlPath);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_isEnabled();
    }

    /**
     * @return int
     */
    protected function _isEnabled()
    {
        return $this->_getStoreConfig(self::FASTERP_ENABLED);
    }

    /**
     * @return string|null
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param $token
     */
    public function setAccessToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     */
    public function createAuthUrl() {}

    public function getProducts()
    {
        try {
            return $this->api($this->_getProductEndpoint());
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param $endpoint
     * @param $code
     * @param string $method
     * @param array $params
     *
     * @return mixed
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function api($endpoint, $method = 'GET', $params = [])
    {
        if (empty($this->token)) {
            $this->getAccessToken();
        }

        $method = strtoupper($method);

        $headers = [
            'Authorization' => "Bearer " . $this->token,
            'Content-Type'  => 'application/json',
        ];

        if (isset($params['magentoOrderId'])) {
            $orderId = $params['magentoOrderId'];
            unset($params['magentoOrderId']);
        }

        $this->logger->info('==================START REQUEST===========================');
        $this->logger->info('==================PARAM INFORMATION=======================');
        $this->logger->info(json_encode($params));
        $this->logger->info('==================RESPONSE INFORMATION====================');

        $response        = $this->sendRequest($endpoint, $method, $params, $headers);
        $messageResponse = $response->getMessage();
        $this->logger->info($messageResponse);
        $this->logger->info($response->getBody() ?? "FAILED TO READ RESPONSE BODY");
        $this->logger->info('==================END REQUEST=============================');

        try {
            $historyLog = $this->erpHistoryLogFactory->create();

            $historyLog->setData('order_id', $orderId ?? null);
            $historyLog->setData('note', $messageResponse);
            $historyLog->setData('type_erp', $this->getTypeOfErpByUrl($endpoint));
            if ($messageResponse === 'OK') {
                $historyLog->setData('status', 1);
            } else {
                $historyLog->setData('status', 0);
            }

            $this->erpHistoryLogResourceModel->save($historyLog);
        } catch (\Exception $exception) {
            $this->logger->error("ERROR WHEN SAVE LOG");
            $this->logger->error($exception->getMessage());
        }

        return $this->processResponse($response);
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        if (empty($this->token)) {
            $this->token = $this->fetchAccessToken();
        }
        return $this->token;
    }

    /**
     * @return false|mixed
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    protected function fetchAccessToken()
    {
        $params = [
            'client_id'     => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'grant_type'    => 'client_credentials',
        ];

        $response = $this->processResponse(
            $this->sendRequest(
                $this->_getTokenEndpoint(),
                'POST',
                $params,
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            )
        );

        return $response['access_token'] ?? false;
    }

    /**
     * @return string|null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    protected function _getClientId()
    {
        return $this->_getStoreConfig(self::FASTERP_CLIENT_ID);
    }

    /**
     * @param \Zend_Http_Response $response
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function processResponse($response)
    {
        $decodedResponse = json_decode($response->getBody(), true);
        if (empty($decodedResponse)) {
            $parsed_response = [];
            parse_str($response->getBody(), $parsed_response);
            $decodedResponse = json_decode(json_encode($parsed_response), true);
        }

        return $decodedResponse;
    }

    /**
     * @param $endpoint
     * @param string $method
     * @param array $params
     * @param array $headers
     *
     * @return \Zend_Http_Response
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    protected function sendRequest($endpoint, $method = 'GET', $params = [], $headers = [])
    {
        $client  = $this->_httpClientFactory->create();
        $headers = array_merge([
            'Accept'     => '*/*',
            'User-Agent' => 'PostmanRuntime/7.28.4'
        ], $headers);
        $client->setHeaders($headers);
        $url = $this->_getBaseUrl() . $endpoint;
        $client->setUri($url);
        switch ($method) {
            case 'GET':
            case 'DELETE':
                $client->setParameterGet($params);
                break;
            case 'POST':
                $client->setParameterPost($params);
                if (isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json') {
                    $client->setRawData(json_encode($params), 'application/json');
                }
                break;
            default:
                throw new LocalizedException(
                    __('Required HTTP method is not supported.')
                );
        }
        $response = $client->request($method);
        if ($response->isError()) {
            $status = $response->getStatus();
            if (($status == 400 || $status == 401)) {
                $decodedResponse = $this->processResponse($response);
                if (isset($decodedResponse['error']['message'])) {
                    $message = $decodedResponse['error']['message'];
                } elseif (isset($decodedResponse['errors'])) {
                    $message = json_encode($decodedResponse['errors'], JSON_UNESCAPED_UNICODE);
                } else {
                    $message = __('Unspecified OAuth error occurred.' . json_encode($decodedResponse));
                }
                throw new LocalizedException(__($message));
            } else {
                $message = sprintf(
                    __('HTTP error %d occurred while issuing request.'),
                    $status
                );
                throw new LocalizedException(__($message));
            }
        }

        return $response;
    }

    /**
     * @return int
     */
    protected function _getBaseUrl()
    {
        return $this->_getStoreConfig(self::FASTERP_BASE_URL);
    }

    /**
     * @return int
     */
    protected function _getTokenEndpoint()
    {
        return $this->_getStoreConfig(self::FASTERP_TOKEN_ENDPOINT);
    }

    /**
     * @param $url
     *
     * @return int
     */
    private function getTypeOfErpByUrl($url)
    {
        switch ($url) {
            case $this->_getProductEndpoint():
                return 1;
            case $this->_getOrderEndpoint():
                return 2;
            case $this->stockEndpoint($url):
                return 3;
            case $this->_getWarehouseEndpoint():
                return 4;
        }
        return 0;
    }

    protected function _getProductEndpoint()
    {
        return $this->_getStoreConfig(self::FASTERP_PRODUCT_ENDPOINT);
    }

    protected function _getOrderEndpoint()
    {
        return $this->_getStoreConfig(self::FASTERP_ORDER_ENDPOINT);
    }

    /**
     * @param $url
     *
     * @return string
     */
    private function stockEndpoint($url)
    {
        if (preg_match('/api\/warehouses\/\w*\/availableinventory/', $url)) {
            return $url;
        }
        return null;
    }

    protected function _getWarehouseEndpoint()
    {
        return $this->_getStoreConfig(self::FASTERP_WAREHOUSES_ENDPOINT);
    }

    public function updateOrder($params)
    {
        try {
            return $this->api($this->_getOrderEndpoint(), "POST", $params);
        } catch (\Exception $exception) {
            $historyLog = $this->erpHistoryLogFactory->create();

            $historyLog->setData('order_id', $params['magentoOrderId']);
            $historyLog->setData('note', $exception->getMessage());
            $historyLog->setData('type_erp', 2);
            $historyLog->setData('status', 0);

            $this->erpHistoryLogResourceModel->save($historyLog);

            return [
                'status'  => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function getWarehouses()
    {
        try {
            return $this->api($this->_getWarehouseEndpoint());
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param $warehouseId
     *
     * @return mixed
     */
    public function getStock($warehouseId)
    {
        try {
            return $this->api(
                preg_replace(self::REGEX_PATH_PARAMETERS, $warehouseId, $this->_getStockEndpoint()),
            );
        } catch (\Exception $exception) {
            return [];
        }
    }

    protected function _getStockEndpoint()
    {
        return $this->_getStoreConfig(self::FASTERP_STOCK_ENDPOINT);
    }

    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @param array $headers
     *
     * @return mixed
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    protected function _httpRequest($url, $method = 'GET', $params = [], $headers = [])
    {
        return $this->processResponse($this->sendRequest($url, $method, $params, $headers));
    }
}
