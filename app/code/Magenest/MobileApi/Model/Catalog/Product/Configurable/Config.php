<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Product\Configurable;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Catalog\Product\Configurable\ConfigInterface;

/**
 * Class Config
 * @package Magenest\MobileApi\Model\Catalog\Product\Configurable
 */
class Config extends AbstractSimpleObject implements ConfigInterface
{
    /** Const */
    const KEY_INDEX = 'index';
    const KEY_OPTION_PRICES = 'option_prices';
    const KEY_IMAGES = 'images';

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->_get(self::KEY_INDEX);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionPrices()
    {
        return $this->_get(self::KEY_OPTION_PRICES);
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->_get(self::KEY_IMAGES);
    }

    /**
     * {@inheritdoc}
     */
    public function setIndex($index)
    {
        return $this->setData(self::KEY_INDEX, $index);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionPrices($optionPrices)
    {
        return $this->setData(self::KEY_OPTION_PRICES, $optionPrices);
    }

    /**
     * {@inheritdoc}
     */
    public function setImages($images)
    {
        return $this->setData(self::KEY_IMAGES, $images);
    }
}
