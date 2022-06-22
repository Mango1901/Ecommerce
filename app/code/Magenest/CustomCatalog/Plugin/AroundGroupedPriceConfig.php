<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 09/12/2021
 * Time: 15:08
 */

namespace Magenest\CustomCatalog\Plugin;


use Magento\Catalog\Block\Product\View;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class AroundGroupedPriceConfig extends View
{

    /**
     * @param \Magento\Catalog\Block\Product\View $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundGetJsonConfig(\Magento\Catalog\Block\Product\View $subject, callable $proceed)
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();
        $tierPrices = [];
        $priceInfo = $product->getPriceInfo();
        $tierPricesList = $priceInfo->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPriceData = [
                'qty' => $tierPrice['price_qty'],
                'price' => $tierPrice['website_price'],
            ];
            $tierPrices[] = $tierPriceData;
        }

        if (!$this->hasOptions()) {
            if ($product->getTypeId() != Grouped::TYPE_CODE) {
                $config = [
                    'productId' => $product->getId(),
                    'priceFormat' => $this->_localeFormat->getPriceFormat(),
                    'tierPrices' => $tierPrices
                ];
                return $this->_jsonEncoder->encode($config);
            }
        }
        $regularPrice = false;
        $finalPrice = false;
        if ($product->getTypeId() != Grouped::TYPE_CODE) {
            $regularPrice = $priceInfo->getPrice('regular_price')->getAmount();
            $finalPrice = $priceInfo->getPrice('final_price')->getAmount();
        }
        $config = [
            'productId'   => (int)$product->getId(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
            'prices'      => [
                'baseOldPrice' => [
                    'amount'      => $regularPrice ? $regularPrice->getBaseAmount() * 1 : 0,
                    'adjustments' => []
                ],
                'oldPrice'   => [
                    'amount'      => $regularPrice ? $regularPrice->getValue() * 1 : 0,
                    'adjustments' => []
                ],
                'basePrice'  => [
                    'amount'      => $finalPrice ? $finalPrice->getBaseAmount() * 1 : 0,
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount'      => $finalPrice ? $finalPrice->getValue() * 1 : 0,
                    'adjustments' => []
                ]
            ],
            'idSuffix'    => '_clone',
            'tierPrices'  => $tierPrices
        ];

        $responseObject = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->_jsonEncoder->encode($config);
    }
}
