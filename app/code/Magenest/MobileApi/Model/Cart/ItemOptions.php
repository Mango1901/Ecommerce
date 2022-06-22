<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Cart;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Cart\ItemOptionsInterface;

/**
 * Class ItemOptions
 * @package Magenest\MobileApi\Model\Cart
 */
class ItemOptions extends AbstractSimpleObject implements ItemOptionsInterface
{
    /** @const */
    const KEY_VALUE = 'value';
    const KEY_LABEL = 'label';

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->_get(self::KEY_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        return $this->setData(self::KEY_LABEL, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get(self::KEY_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::KEY_VALUE, $value);
    }
}
