<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Pricing\Render;

use Magento\Catalog\Pricing\Render\FinalPriceBox as CatalogRender;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class for final_price rendering
 */
class FinalPriceBox extends CatalogRender
{
    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Min max values
     *
     * @var array
     */
    protected $_minMax = [];

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->findMinMaxValue();
    }

    /**
     * @return AmountFactory
     */
    public function getAmountFactory()
    {
        if (is_null($this->amountFactory)) {
            $this->amountFactory = ObjectManager::getInstance()->get(AmountFactory::class);
        }

        return $this->amountFactory;
    }

    /**
     * @return PriceCurrencyInterface
     */
    protected function getPriceCurrency()
    {
        if (is_null($this->priceCurrency)) {
            $this->priceCurrency = ObjectManager::getInstance()->get(PriceCurrencyInterface::class);
        }

        return $this->priceCurrency;
    }

    /**
     * @return AmountInterface
     */
    public function getMinPrice()
    {
        $minPrice = $this->getPriceCurrency()->convert($this->_minMax['min']);

        return $this->getAmountFactory()->create($minPrice);
    }

    /**
     * @return AmountInterface
     */
    public function getMaxPrice()
    {
        $maxPrice = $this->getPriceCurrency()->convert($this->_minMax['max']);

        return $this->getAmountFactory()->create($maxPrice);
    }

    /**
     * @return bool
     */
    public function isFixedPrice()
    {
        return !$this->saleableItem->getAllowCreditRange() && $this->saleableItem->getCreditAmount();
    }

    /**
     * @return $this
     */
    protected function findMinMaxValue()
    {
        $rate = $this->saleableItem->getCreditRate() ? $this->saleableItem->getCreditRate() / 100 : 1;

        $min = $max = $this->saleableItem->getCreditAmount() * $rate;

        if ($this->saleableItem->getAllowCreditRange()) {
            $min = $this->saleableItem->getMinCredit() * $rate;
            $max = $this->saleableItem->getMaxCredit() * $rate;
        }

        $this->_minMax = [
            'min' => $min,
            'max' => $max
        ];

        return $this;
    }
}
