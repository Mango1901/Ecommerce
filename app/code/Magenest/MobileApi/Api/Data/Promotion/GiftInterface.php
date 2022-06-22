<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Promotion;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface GiftInterface
 * @package Magenest\MobileApi\Api\Data\Promotion
 */
interface GiftInterface extends ExtensibleDataInterface
{
    /**
     * Get icon
     *
     * @return string
     * @since 102.0.0
     */
    public function getIcon();

    /**
     * Set icon
     *
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon);

    /**
     * Get title
     *
     * @return string
     * @since 102.0.0
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get items
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[] $items
     * @return $this
     */
    public function setItems($items);
}
