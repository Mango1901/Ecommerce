<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\Core\Helper;

use Magenest\StoreCredit\Model\Product\Type\StoreCredit;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Pricing\SaleableInterface;

class CatalogHelper extends AbstractHelper
{
    /**
     * @param Product $_product
     * @return bool|float
     */
    public static function getSalesPercent($_product)
    {
        return self::getDiscountPercent($_product);
    }

    public static function getSalesAmount($_product)
    {
        if ($_product instanceof Product) {
            if (($_product->getTypeId() !== 'bundle')) {
                $regularPrice = $_product->getPriceInfo()->getPrice(RegularPrice::PRICE_CODE)->getAmount();
                $finalPrice = $_product->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getAmount();

                $specialPrice = $finalPrice->getValue();
                $price = $regularPrice->getValue();

                return $price-$specialPrice;
            }
        }
        return false;
    }

    public static function compareDateRange($today, $from, $to)
    {
        return ($today >= strtotime($from) && $today <= strtotime($to)) || ($today >= strtotime($from) && is_null($to)) || (is_null($from) && $today <= strtotime($to)) || (is_null($from) && is_null($to));
    }

    /**
     * @param Product|SaleableInterface $_product
     * @return bool
     */
    public static function isProductNew($_product)
    {
        if (!$_product instanceof Product) {
            return false;
        }
        //Display new when turn on is_new or set new from date, new to date.
        if ($_product->getIsNew()) {
            return true;
        }else {
            $newFromDate = $_product->getNewsFromDate()?:0;
            $newToDate = $_product->getNewsToDate()?:0;
            if (static::compareDateRange(time(), $newFromDate, $newToDate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $product
     * @return float|null
     */
    public static function getDiscountPercent($product)
    {
        $discountPercent = null;
        if ($product instanceof Product) {
            switch ($product->getTypeId()) {
                case Product\Type::TYPE_SIMPLE:
                case StoreCredit::TYPE_STORE_CREDIT:
                case Product\Type::TYPE_VIRTUAL:
                    $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
                    $specialPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
                    $discountPercent = self::getDiscountPercentProduct($regularPrice, $specialPrice);
                    break;
                case 'configurable':
                    $basePrice = $product->getPriceInfo()->getPrice('regular_price');
                    $regularPrice = $basePrice->getMinRegularAmount()->getValue();
                    $specialPrice = $product->getFinalPrice();
                    $discountPercent = self::getDiscountPercentProduct($regularPrice, $specialPrice);
                    break;
                case Product\Type::TYPE_BUNDLE:
                    $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getMinimalPrice()->getValue();
                    $specialPrice = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
                    $discountPercent = self::getDiscountPercentProduct($regularPrice, $specialPrice);
                    break;
                case 'grouped':
                    $usedProds = $product->getTypeInstance(true)->getAssociatedProducts($product);
                    $regularPrice = 0;
                    $specialPrice = 0;
                    foreach ($usedProds as $child) {
                        if ($child->getId() != $product->getId()) {
                            $regularPrice += $child->getPrice();
                            $specialPrice += $child->getFinalPrice();
                        }
                    }
                    $discountPercent = self::getDiscountPercentProduct($regularPrice, $specialPrice);
                    break;
            }
        }
        return $discountPercent;
    }

    /**
     * @param $regularPrice
     * @param $specialPrice
     * @return string|null
     */
    public static function getDiscountPercentProduct($regularPrice, $specialPrice)
    {
        if ($regularPrice && $specialPrice) {
            $discount = ($regularPrice - $specialPrice) / $regularPrice * 100;
            $discountPercent = round($discount);
            return  $discountPercent ?? null;
        }
        return null;
    }
}
