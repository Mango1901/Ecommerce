<?php

namespace Magenest\SellOnInstagram\Model;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magenest\SellOnInstagram\Logger\Logger;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\SellOnInstagram\Model\ResourceModel\Mapping as MappingResourceModel;
use Magento\Framework\UrlInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;

class BatchBuilder
{
    const MAX_VERSION = 2147483647;
    const MIN_VERSION = 1;
    const DEFAULT_BRAND = 'No brand';
    const STATUS_TRACKING = 1;
    protected $_ignoreAttr = [
        'price',
        'sale_price',
        'url',
        'image_url',
        'additional_image_urls'
    ];
    /**
     * @var UrlBuilder
     */
    protected $urlBuilder;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Image
     */
    protected $image;
    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSet;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var false|string
     */
    protected $minDate;
    /**
     * @var false|string
     */
    protected $maxDate;
    /**
     * @var MappingResourceModel
     */
    protected $mappingResource;
    /**
     * @var UrlInterface
     */
    protected $url;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;
    /**
     * @var \Magenest\SellOnInstagram\Helper\Helper
     */
    protected $helper;
    /**
     * @var \Magenest\SellOnInstagram\Helper\Data
     */
    protected $helperData;
    /**
     * @var StockResolverInterface
     */
    protected $stockResolver;
    /**
     * @var GetProductSalableQtyInterface
     */
    protected $getProductSalableQty;

    public function __construct(
        UrlBuilder $urlBuilder,
        StoreManagerInterface $storeManager,
        AttributeSetRepositoryInterface $attributeSet,
        Image $image,
        Logger $logger,
        MappingResourceModel $mappingResource,
        UrlInterface $url,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magenest\SellOnInstagram\Helper\Helper $helper,
        \Magenest\SellOnInstagram\Helper\Data $helperData,
        StockResolverInterface $stockResolver,
        GetProductSalableQtyInterface $getProductSalableQty
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->attributeSet = $attributeSet;
        $this->image = $image;
        $this->logger = $logger;
        $this->minDate = date('Y-m-d', BatchBuilder::MIN_VERSION);
        $this->maxDate = date('Y-m-d', BatchBuilder::MAX_VERSION);
        $this->mappingResource = $mappingResource;
        $this->url = $url;
        $this->filterProvider = $filterProvider;
        $this->helper = $helper;
        $this->helperData = $helperData;
        $this->stockResolver = $stockResolver;
        $this->getProductSalableQty = $getProductSalableQty;
    }

    /**
     * @param $accessToken
     * @param $requests
     *
     * @return array
     */
    public function createProductItemTemplate($accessToken, $requests)
    {
        return [
            "access_token" => $accessToken,
            "item_type" => "PRODUCT_ITEM",
            "requests" => $requests
        ];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function requestCreateProductAction(Product $product, $templateId)
    {
        try {
            $maps = $this->mappingResource->getAllFieldMap($templateId);
            $productDescription = $this->helper->getInstagramDescription($this->filterProvider->getPageFilter()->filter($product->getDescription()));
            $stockId = $this->stockResolver->execute(\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE, $this->storeManager->getWebsite()->getCode())->getStockId();
            $stockQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);
            if ($this->getValueAttribute($product, 'country_of_manufacture') != " ") {
                $brand = $this->getValueAttribute($product, 'country_of_manufacture');
            } else {
                $brand = self::DEFAULT_BRAND;
            }
            $trackingOrder = $this->helperData->enableTrackingOrder();
            if ($trackingOrder) {
                $url = $this->url->getUrl(
                    'sell_instagram/checkout/addtocart/product/',
                    ['product' => $product->getId(),
                        'from_shop' => self::STATUS_TRACKING
                    ]
                );
            } else {
                $url = $this->url->getUrl(
                    'sell_instagram/checkout/addtocart/product/',
                    ['product' => $product->getId()]
                );
            }
            $data = [
                "availability" => $product->isInStock() ? __('in stock')->__toString() : __('out of stock')->__toString(),
                "brand" => $brand,
                "description" => $productDescription ?: __("No description")->__toString(),
                "image_url" => $this->formatUrlImage() . 'catalog/product' . $product->getImage(),
                "name" => $product->getName(),
                "price" => $this->getPrice($product->getPrice()),
                "currency" => $this->storeManager->getStore()->getCurrentCurrencyCode(),
                "condition" => $this->helperData->getProductCondition(),
                "url" => $url,
                "color" => $this->getValueAttribute($product, 'color'),
                "size" => $this->getValueAttribute($product, 'size'),
                "product_type" => $product->getTypeId(),
                "inventory" => $stockQty,
            ];
            if ($this->helperData->isAllowSpecialPrice()) {
                $dataMF["sale_price"] = !empty($product->getSpecialPrice())
                    ? $this->getPrice($product->getSpecialPrice())
                    : $this->getPrice($product->getFinalPrice());
                if (!empty($product->getSpecialFromDate()) && !empty($product->getSpecialToDate())) {
                    $dataMF["sale_price_start_date"] = $product->getSpecialFromDate();
                    $dataMF["sale_price_end_date"] = $product->getSpecialToDate();
                }
                $data = array_merge($dataMF, $data);
            }
            if (!empty($maps)) {
                $mapMF = [];
                foreach ($maps as $map) {
                    $mapMF[$map['fb_attribute']] = $this->getValueAttribute($product, $map['magento_attribute']);
                    if (!in_array($map['fb_attribute'], $this->getIgnoreAttr())) {
                        unset($data[$map['fb_attribute']]);
                    }
                }
                $data = array_merge($mapMF, $data);
            }

            return [
                "method" => "CREATE",
                "retailer_id" => $product->getSku(),
                "data" => $data
            ];
        } catch (NoSuchEntityException $e) {
            $this->logger->error("Error prepare data to sync: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return [];
    }

    /**
     * @return array
     */
    public function getIgnoreAttr()
    {
        return $this->_ignoreAttr;
    }

    private function formatUrlImage()
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param $price
     *
     * @return float|int
     */
    private function getPrice($price)
    {
        return (int)$price * 100;
    }

    /**
     * @param Product $product
     * @param $code
     *
     * @return bool|null
     */
    private function getValueAttribute($product, $code)
    {
        $attribute = $product->getResource()->getAttribute($code);
        if ($attribute === false) {
            return "";
        }
        $value = $attribute->getFrontend()->getValue($product);
        if ($code == 'gender') {
            switch ($value) {
                case 'Men':
                case 'Boys':
                    return 'male';
                case 'Women':
                case 'Girls':
                    return 'female';
                case 'Unisex':
                    return 'unisex';
                default:
                    return '';
            }
        } else {
            return $value != "0" ? $value : "";
        }
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function requestDeleteProductAction(Product $product)
    {
        return [
            "method" => "DELETE",
            "retailer_id" => $product->getSku()
        ];
    }
}
