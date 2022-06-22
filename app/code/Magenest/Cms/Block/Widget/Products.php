<?php

namespace Magenest\Cms\Block\Widget;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;

class Products extends ProductSlider
{
    /**
     * @var \Magenest\Cms\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_collection;

    public function __construct(
        \Magenest\Cms\Model\Product $productModel,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = [],
        ?Json $json = null,
        ?LayoutFactory $layoutFactory = null,
        ?EncoderInterface $urlEncoder = null
    ) {
        $this->_productModel = $productModel;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $priceCurrency,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
        );
    }

    protected function _beforeToHtml()
    {
        $catIds = [];
        $categories = $this->getConfig("categories");
        if ($categories!='') {
            $catIds = explode(",", $categories);
        }
        $this->setTemplate('widget/additional_product_slider.phtml');
        $source_key = $this->getConfig("product_source");
        $config = [];
        $config['pagesize'] = $this->getConfig('products_count', 12);
        $config['cats'] = $catIds;
        $collection = $this->_productModel->getProductBySource($source_key, $config);

        $this->_collection = $collection;
        return parent::_beforeToHtml();
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'BLOCK_TPL',
            $this->_storeManager->getStore()->getCode(),
            $this->getTemplateFile(),
            'base_url' => $this->getBaseUrl(),
            'template' => $this->getTemplate(),
            $this->getConfig("products_count")
        ];
    }

    public function getConfig($key, $default = '')
    {
        if ($this->hasData($key) && $this->getData($key)) {
            return $this->getData($key);
        }
        return $default;
    }

    /**
     * Check product is new
     *
     * @param  Mage_Catalog_Model_Product $_product
     * @return bool
     */
    public function checkProductIsNew($_product = null)
    {
        $from_date = $_product->getNewsFromDate();
        $to_date = $_product->getNewsToDate();
        $is_new = false;
        $is_new = $this->isNewProduct($from_date, $to_date);
        $today = strtotime("now");

        if ($from_date && $to_date) {
            $from_date = strtotime($from_date);
            $to_date = strtotime($to_date);
            if ($from_date <= $today && $to_date >= $today) {
                $is_new = true;
            }
        } elseif ($from_date && !$to_date) {
            $from_date = strtotime($from_date);
            if ($from_date <= $today) {
                $is_new = true;
            }
        } elseif (!$from_date && $to_date) {
            $to_date = strtotime($to_date);
            if ($to_date >= $today) {
                $is_new = true;
            }
        }
        return $is_new;
    }

    public function getProductCollection()
    {
        return $this->_collection;
    }

    public function isNewProduct($created_date, $num_days_new = 3)
    {
        $check = false;

        $startTimeStamp = strtotime($created_date);
        $endTimeStamp = strtotime("now");

        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff/86400;// 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        if ($numberDays <= $num_days_new) {
            $check = true;
        }

        return $check;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getVesProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
        ? $arguments['price_id']
        : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
        ? $arguments['include_container']
        : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
        ? $arguments['display_minimal_price']
        : true;
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
