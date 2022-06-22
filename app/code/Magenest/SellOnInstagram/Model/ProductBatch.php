<?php

namespace Magenest\SellOnInstagram\Model;

use Exception;
use Magenest\SellOnInstagram\Model\BatchBuilder;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Catalog\Model\Product\Action;
use Magenest\SellOnInstagram\Helper\Data;
use Magenest\SellOnInstagram\Logger\Logger;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\Exception\LocalizedException;
use Magenest\SellOnInstagram\Model\Config\Source\Status;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramProduct;

class ProductBatch
{
    const TYPE = '1';
    const CREATE = '1';
    const DELETE = '2';

    protected $productsSelected = [];

    protected $countItemsSuccess = 0;

    protected $countItemsFail = 0;

    protected $errors;

    protected $action = 1;

    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var \Magenest\SellOnInstagram\Model\BatchBuilder
     */
    protected $batchBuilder;
    /**
     * @var mixed
     */
    protected $accessToken;
    /**
     * @var mixed
     */
    protected $catalogId;
    /**
     * @var Product
     */
    protected $productResource;
    /**
     * @var Action
     */
    protected $productAction;
    /**
     * @var History
     */
    protected $history;
    /**
     * @var InstagramProduct
     */
    protected $instagramProductResource;

    public function __construct(
        Data $helper,
        Logger $logger,
        Curl $curl,
        BatchBuilder $batchBuilder,
        Product $productResource,
        Action $productAction,
        History $history,
        InstagramProduct $instagramProductResource
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->curl = $curl;
        $this->batchBuilder = $batchBuilder;
        $this->accessToken = $this->helper->getAccessToken();
        $this->catalogId = $this->helper->getCatalogId();
        $this->productResource = $productResource;
        $this->productAction = $productAction;
        $this->history = $history;
        $this->instagramProductResource = $instagramProductResource;
    }

    /**
     * @param $data
     * @param $productIds
     */
    public function syncProductToFbShop($data, $feedId)
    {

        if (!empty($this->catalogId)) {
            $url = "https://graph.facebook.com/v12.0/$this->catalogId/batch";
            try {
                $this->sendPost($url, $data);
                $this->validationStatus($this->helper->unserialize($this->curl->getBody()));
                $this->history->updateReport($this, $feedId, true);
            } catch (LocalizedException $e) {
                $this->logger->error("Validate Status Error after send Api: " . $e->getMessage());
            } catch (Exception $e) {
                $this->logger->error("Send Post Error: " . $e->getMessage());
            }
        } else {
            $this->logger->critical("Facebook Catalog Id is empty");
        }
    }

    /**
     * @param $url
     * @param $data
     *
     * @throws Exception
     */
    private function sendPost($url, $data)
    {
        $this->curl->post($url, $data);
        $this->logger->critical('Result ' . print_r([$this->curl->getBody()], true));
    }
    /**
     * @return int
     */
    public function getCountItemsSuccess()
    {
        return $this->countItemsSuccess;
    }

    /**
     * @param $items
     *
     * @return $this
     */
    public function setCountItemsSuccess($items)
    {
        $this->countItemsSuccess = count($items);
        $this->productsSelected = $items;

        return $this;
    }
    /**
     * @return array
     */
    public function getProductsSelected()
    {
        return $this->productsSelected;
    }

    /**
     * @return int
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountItemsFail()
    {
        return $this->countItemsFail;
    }

    /**
     * @param $items
     *
     * @return $this
     */
    public function setCountItemsFail($items)
    {
        $this->countItemsFail = $items;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }
    /**
     * @param $data
     *
     * @throws LocalizedException
     */
    private function validationStatus($data)
    {
        if (isset($data['error'])) {
            $this->countItemsFail = $this->getCountItemsSuccess();
            $this->countItemsSuccess = 0;
            $message = isset($data['error']['message']) ? $data['error']['message'] : "Some error";
            $this->errors = __($message);
        } else {
            if (isset($data['validation_status'])) {
                $skus = [];
                $errors = $data['validation_status'];
                foreach ($errors as $error) {
                    if (isset($error['retailer_id'])) {
                        $skus [] = $error['retailer_id'];
                        if (isset($error['errors'][0]['message'])) {
                            $message = $error['errors'][0]['message'];
                            $this->errors = __($message);
                        }
                    }
                }
                $productIds = $this->productResource->getProductsIdsBySkus($skus);
                $this->countItemsFail = count($productIds);
                $this->countItemsSuccess = $this->getCountItemsSuccess() - $this->countItemsFail;
            }
        }
    }
}
