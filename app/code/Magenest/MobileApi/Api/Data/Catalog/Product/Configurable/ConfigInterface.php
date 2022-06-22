<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Catalog\Product\Configurable;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ConfigInterface
 * @package Magenest\MobileApi\Api\Data\Catalog\Product\Configurable
 */
interface ConfigInterface extends ExtensibleDataInterface
{
    /**
     * Get index
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getIndex();

    /**
     * Set index
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[] $index
     * @return $this
     */
    public function setIndex($index);

    /**
     * Get option prices
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getOptionPrices();

    /**
     * Set option prices
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[] $optionPrices
     * @return $this
     */
    public function setOptionPrices($optionPrices);

    /**
     * Set images
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[] $images
     * @return $this
     */
    public function setImages($images);

    /**
     * Get images
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getImages();
}
