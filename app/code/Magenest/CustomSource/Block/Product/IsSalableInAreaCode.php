<?php

namespace Magenest\CustomSource\Block\Product;

use Magenest\CustomSource\Helper\Data;
use Magenest\CustomSource\Plugin\SetAreaCodeContext;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class IsSalableInAreaCode extends AbstractView
{
    /**
     * @var array|null
     */
    protected $_datas = null;

    /**
     * Customer session
     *
     * @var Context
     */
    protected $httpContext;

    /**
     * @var Data
     */
    protected $dataHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        ArrayUtils $arrayUtils,
        Context $httpContext,
        Data $dataHelper,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * @param $product
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isInOfStock($product)
    {
        $areaCode = $this->dataHelper->getCurrentArea();
        if ($areaCode) {
            if (!$this->_datas) {
                $this->getInformationStockItemByArea($areaCode);
            }
            $skus = [];
            if ($product->getTypeId() == "configurable") {
                $this->getListSkuOfProductConfigurable($product, $skus);
            } else {
                $skus[] = $product->getSku();
            }
            $isInStock = false;

            foreach ($skus as $sku) {
                if (isset($this->_datas[$sku]) && $this->_datas[$sku]['is_in_stock']) {
                    $isInStock = true;
                    break;
                }
            }

            if ($isInStock) {
                return "In Stock";
            } else {
                return "Out Of Stock";
            }
        }
        return false;
    }

    /**
     * @param $areaCode
     * @return $this
     */
    public function getInformationStockItemByArea($areaCode)
    {
        try {
            /**
             * @var $productCollection Collection
             */
            $productCollection = $this->getProductCollection();
            $skus = [];
            foreach ($productCollection as $product) {
                if ($product->getTypeId() == "configurable") {
                    $this->getListSkuOfProductConfigurable($product, $skus);
                } else {
                    $skus[] = $product->getSku();
                }
            }

            $inventorySourceItems = $this->dataHelper->getDataIsSalableOfProduct($areaCode, $skus);

            $this->_datas = [];

            foreach ($inventorySourceItems as $sourceItem) {
                $this->_datas[$sourceItem['sku']] = [
                    'is_in_stock' => $sourceItem['is_in_stock'],
                    'qty' => $sourceItem['qty'],
                ];
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return $this;
    }

    /**
     * @param $product
     * @param $skus
     */
    private function getListSkuOfProductConfigurable($product, &$skus)
    {
        $childProduct = $product->getTypeInstance()->getUsedProducts($product);
        foreach ($childProduct  as $child) {
            $skus[] = $child->getSku();
        }
    }
}
