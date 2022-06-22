<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Cart;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;
use Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface;

/**
 * Interface ItemOptionsInterface
 * @package Magenest\MobileApi\Api\Data\Cart
 */
interface ItemOptionsInterface extends ExtensibleDataInterface
{
    /**
     * Get label
     *
     * @return string
     * @since 102.0.0
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get value
     *
     * @return string
     * @since 102.0.0
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);
}
