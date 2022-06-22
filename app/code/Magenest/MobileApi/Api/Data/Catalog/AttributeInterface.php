<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Catalog;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AttributeInterface
 * @package Magenest\MobileApi\Api\Data\Catalog
 */
interface AttributeInterface extends ExtensibleDataInterface
{
    /**
     * Get label
     *
     * @return string
     * @since 102.0.0
     */
    public function getLabel();

    /**
     * Get label
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
     * Get value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);
}
