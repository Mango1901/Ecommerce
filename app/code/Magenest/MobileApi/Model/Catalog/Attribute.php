<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Catalog\AttributeInterface;

/**
 * Class Attribute
 * @package Magenest\MobileApi\Model\Catalog
 */
class Attribute extends AbstractSimpleObject implements AttributeInterface
{
    /** @const */
    const KEY_LABEL = 'label';
    const KEY_VALUE = 'value';

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
