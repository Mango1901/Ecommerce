<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Catalog\PricingInterface;

/**
 * Class Pricing
 * @package Magenest\MobileApi\Model\Catalog
 */
class Pricing extends AbstractSimpleObject implements PricingInterface
{
    /** @const */
    const KEY_REGULAR_PRICE = 'regular_price';
    const KEY_REGULAR_MINIMAL_PRICE = 'regular_minimal_price';
    const KEY_REGULAR_MAXIMAL_PRICE = 'regular_maximal_price';
    const KEY_FINAL_PRICE = 'final_price';
    const KEY_FINAL_MINIMAL_PRICE = 'final_minimal_price';
    const KEY_FINAL_MAXIMAL_PRICE = 'final_minimal_price';

    /**
     * {@inheritdoc}
     */
    public function getRegularPrice()
    {
        return $this->_get(self::KEY_REGULAR_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegularPrice($regularPrice)
    {
        return $this->setData(self::KEY_REGULAR_PRICE, $regularPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegularMinimalPrice()
    {
        return $this->_get(self::KEY_REGULAR_MINIMAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegularMinimalPrice($regularMinimalPrice)
    {
        return $this->setData(self::KEY_REGULAR_MINIMAL_PRICE, $regularMinimalPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegularMaximalPrice()
    {
        return $this->_get(self::KEY_REGULAR_MAXIMAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegularMaximalPrice($regularMaximalPrice)
    {
        return $this->setData(self::KEY_REGULAR_MAXIMAL_PRICE, $regularMaximalPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalPrice()
    {
        return $this->_get(self::KEY_FINAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFinalPrice($finalPrice)
    {
        return $this->setData(self::KEY_FINAL_PRICE, $finalPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalMinimalPrice()
    {
        return $this->_get(self::KEY_FINAL_MINIMAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFinalMinimalPrice($finalMinimalPrice)
    {
        return $this->setData(self::KEY_FINAL_MINIMAL_PRICE, $finalMinimalPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalMaximalPrice()
    {
        return $this->_get(self::KEY_FINAL_MAXIMAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFinalMaximalPrice($finalMaximalPrice)
    {
        return $this->setData(self::KEY_FINAL_MAXIMAL_PRICE, $finalMaximalPrice);
    }
}
