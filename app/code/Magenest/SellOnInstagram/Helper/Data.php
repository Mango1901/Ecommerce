<?php

namespace Magenest\SellOnInstagram\Helper;

use Exception;
use Magenest\SellOnInstagram\Model\ProductBatch;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Serialize\Serializer\Json;
use Magenest\SellOnInstagram\Model\ResourceModel\Mapping as MappingResourceModel;

class Data extends Helper
{
    const XML_PATH_ENABLE = 'sell_on_instagram/connect_instagram/enable';
    const XML_PATH_TOKEN = 'sell_on_instagram/connect_instagram/access_token';
    const XML_PATH_CATALOG_ID = 'sell_on_instagram/connect_instagram/catalog_id';
    const XML_PATH_PRODUCT_CONDITION = 'sell_on_instagram/product_in_shop/product_condition';
    const XML_PATH_PAGE_ID = 'sell_on_instagram/connect_instagram/page_id';
    const XML_PATH_ALLOW_SPECIAL = 'sell_on_instagram/sync_config/special_price_product';
    const XML_PATH_ALLOW_OUT_OF_STOCK = 'sell_on_instagram/sync_config/sync_out_of_stock';
    const XML_PATH_ENABLE_TRACKING_ORDER = 'sell_on_instagram/sync_config/tracking_order';
    const REDIRECT_URI_PATH = 'sell_instagram/connect/getAccessPage/';
    const XML_PATH_CLIENT_ID = 'sell_on_instagram/connect_instagram/client_id';
    const XML_PATH_CLIENT_SECRET = 'sell_on_instagram/connect_instagram/app_secret';
    /**
     * @var Timezone
     */
    protected $timeZone;
    /**
     * @var MappingResourceModel
     */
    protected $mappingResource;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    public function __construct(
        Json $serializer,
        StoreManagerInterface $storeManager,
        Registry $registry,
        Timezone $timeZone,
        MappingResourceModel $mappingResource,
        \Magento\Framework\UrlInterface $url,
        Context $context
    ) {
        parent::__construct($serializer, $storeManager, $registry, $context);
        $this->timeZone = $timeZone;
        $this->mappingResource = $mappingResource;
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getProductAttribute()
    {
        $catalogProduct = $this->mappingResource->getEavIdByType(Product::ENTITY);
        $fields = $this->mappingResource->getFieldsByEntity($catalogProduct['entity_type_id']);
        $attributesCode = array_column($fields, 'attribute_code');
        $attributesLabel = array_column($fields, 'frontend_label');

        return $this->fixFields(array_combine($attributesCode, $attributesLabel));
    }
    /**
     * @param $fields
     *
     * @return array
     */
    private function fixFields($fields)
    {
        $arrayResult = [];
        $fixedFields = $fields;
        foreach ($fields as $attributeCode => $attributeLabel) {
            if (!$attributeCode || !$attributeLabel) {
                unset($fixedFields[$attributeCode]);
            } else {
                $arrayResult[] = [
                    'code' => $attributeCode,
                    'label' => $attributeLabel
                ];
            }
        }

        return $arrayResult;
    }

    /**
     * @param $templateId
     *
     * @return array
     */
    public function getFieldsMappedByTemplateId($templateId)
    {
        try {
            $templateMap = $this->mappingResource->getAllFieldMap($templateId);
        } catch (Exception $exception) {
            $this->debug($exception);
        }

        return $templateMap ? $templateMap : [];
    }
    public function getAccessToken()
    {
        return $this->getStoreConfig(self::XML_PATH_TOKEN, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getCatalogId()
    {
        return $this->getStoreConfig(self::XML_PATH_CATALOG_ID, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @param ProductBatch $productBatch
     *
     * @return Phrase
     */
    public function getSummaryStats(ProductBatch $productBatch)
    {
        $totalItems = $productBatch->getCountItemsSuccess() + $productBatch->getCountItemsFail();
        $message = __(
            '%1 / %2 products', $productBatch->getCountItemsSuccess() , $totalItems
        );

        return $message;
    }
    /**
     * Calculate sync time
     *
     * @param string $time
     *
     * @return string
     */
    public function getExecutionTime($time)
    {
        $reportTime = $this->timeZone->date($time);
        $timeDiff = $reportTime->diff($this->timeZone->date());

        return $timeDiff->format('%H:%I:%S');
    }
    public function isEnableModule()
    {
        return $this->getStoreConfig(self::XML_PATH_ENABLE, ScopeInterface::SCOPE_WEBSITE);
    }
    public function getProductCondition()
    {
        return $this->getStoreConfig(self::XML_PATH_PRODUCT_CONDITION, ScopeInterface::SCOPE_WEBSITE);

    }
    public function getPageId()
    {
        return $this->getStoreConfig(self::XML_PATH_PAGE_ID, ScopeInterface::SCOPE_WEBSITE);
    }
    public function getClientID()
    {
        return $this->getStoreConfig(self::XML_PATH_CLIENT_ID, ScopeInterface::SCOPE_WEBSITE);
    }
    public function getClientSecret()
    {
        return $this->getStoreConfig(self::XML_PATH_CLIENT_SECRET, ScopeInterface::SCOPE_WEBSITE);
    }
    public function isAllowSpecialPrice()
    {
        return $this->getStoreConfig(self::XML_PATH_ALLOW_SPECIAL, ScopeInterface::SCOPE_WEBSITE);
    }
    public function isAllowOutOfStock()
    {
        return $this->getStoreConfig(self::XML_PATH_ALLOW_OUT_OF_STOCK, ScopeInterface::SCOPE_WEBSITE);
    }
    public function getRedirectUri()
    {
        return $this->url->getBaseUrl() . self::REDIRECT_URI_PATH;
    }
    public function enableTrackingOrder()
    {
        return $this->getStoreConfig(self::XML_PATH_ENABLE_TRACKING_ORDER, ScopeInterface::SCOPE_WEBSITE);
    }
}
