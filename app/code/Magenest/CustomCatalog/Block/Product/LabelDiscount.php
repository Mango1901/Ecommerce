<?php

namespace Magenest\CustomCatalog\Block\Product;

use Magenest\Core\Helper\CatalogHelper;
use Magenest\CustomCatalog\Model\Config\Source\TypeSaleLabel;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Label Discount
 */
class LabelDiscount extends Template
{
    const CONFIG_PATH_ENABLE_PRODUCT_DISCOUNT_LABEL = 'catalog/product_discount_label/enable';
    const CONFIG_PATH_TYPE_LABEL_DISCOUNT = 'catalog/product_discount_label/type';
    const CONFIG_PATH_LABEL_DISCOUNT = 'catalog/product_discount_label/sale_label';

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Magenest_CustomCatalog::product_discount_label.phtml';

    /**
     * Get Product Discount label
     * @return string
     */
    public function getProductDiscountLabel()
    {
        if ($this->isProductDiscountLabelEnable() && $sale = CatalogHelper::getSalesPercent($this->getProduct())) {
            if ($this->getTypeLabelDiscount() == TypeSaleLabel::VALUE_LABEL) {
                return (string)$this->getStoreConfig(self::CONFIG_PATH_LABEL_DISCOUNT);
            } elseif ($this->getTypeLabelDiscount() == TypeSaleLabel::VALUE_PERCENT) {
                return '-' . $sale . '%';
            }
        }
        return null;
    }

    /**
     * @return false|float
     */
    public function getSalesAmount()
    {
        return CatalogHelper::getSalesAmount($this->getProduct());
    }

    /**
     * @return bool
     */
    public function isProductNew()
    {
        return CatalogHelper::isProductNew($this->getProduct());
    }

    /**
     * Function generic used for get store config
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getStoreConfig($path, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }

        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Used for get store id
     *
     * @return int|null
     */
    private function getStoreId()
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $storeId = null;
        }

        return $storeId;
    }

    /**
     * @return int
     */
    private function isProductDiscountLabelEnable()
    {
        return (int)$this->getStoreConfig(self::CONFIG_PATH_ENABLE_PRODUCT_DISCOUNT_LABEL);
    }

    /**
     * Get Type Label Of Discount
     * @return int
     */
    private function getTypeLabelDiscount()
    {
        return (int)$this->getStoreConfig(self::CONFIG_PATH_TYPE_LABEL_DISCOUNT);
    }
}
