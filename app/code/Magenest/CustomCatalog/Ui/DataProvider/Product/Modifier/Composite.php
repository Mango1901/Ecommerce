<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomCatalog\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Bundle\Model\Product\Type;
use Zend\I18n\Filter\NumberParse;
use Magenest\CustomCatalog\Pricing\Currency;

/**
 * Class Composite
 * @package Magenest\CustomCatalog\Ui\DataProvider\Product\Modifier
 */
class Composite extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $_locator;

    /**
     * @var Currency
     */
    protected $_currency;

    /**
     * Constructor
     *
     * @param LocatorInterface $locator
     * @param Currency $currency
     */
    public function __construct(
        LocatorInterface $locator,
        Currency $currency
    )
    {
        $this->_locator = $locator;
        $this->_currency = $currency;
    }


    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $product = $this->getProduct();
        $isBundleProduct = $product->getTypeId() === Type::TYPE_CODE;

        if ($product->getId() && $isBundleProduct) {
            $price = $product->getPrice();
            $specialPrice = $product->getSpecialPrice();

            if (!$specialPrice || empty($specialPrice)) {
                return $data;
            }

            $data[$product->getId()]['product']['special_price_amount'] = $this->_currency->roundPrice((min(100, $specialPrice) * $price) / 100);

            foreach ($data[$product->getId()]['product']['tier_price'] as &$record) {
                $record['price'] = $this->_currency->roundPrice($record['price']);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($this->getProduct()->getTypeId() === Type::TYPE_CODE) {
            $specialPriceContainer = &$meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['container_special_price']['children'];
            $specialPriceContainer['special_price']['arguments']['data']['config']['visible'] = false;
            $specialPriceContainer['special_price_amount'] = array_replace_recursive($specialPriceContainer['special_price'], [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'component' => 'Magenest_CustomCatalog/js/bundle/special-price',
                            'code' => 'special_price_amount',
                            'addbefore' => $this->getStore()->getBaseCurrency()->getCurrencySymbol(),
                            'visible' => true,
                            'sortOrder' => 10,
                        ]
                    ]
                ]
            ]);

            $tierPriceRecord = &$meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['tier_price']['children']['record']['children']['price_value']['children'];
            $tierPriceRecord['value_type']['arguments']['data']['config']['prices']['percent'] = '${ $.parentName }.price';
            $tierPriceRecord['price_calc']['arguments']['data']['config']['component'] = 'Magenest_CustomCatalog/js/bundle/tier-price/percentage-processor';
        }

        return $meta;
    }

    /**
     * Retrieve store
     *
     * @return \Magento\Store\Model\Store
     */
    protected function getStore()
    {
        return $this->_locator->getStore();
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct()
    {
        return $this->_locator->getProduct();
    }
}
