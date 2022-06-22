<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Affiliate;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface WidgetInterface
 * @package Magenest\MobileApi\Api\Data\Affiliate
 */
interface WidgetInterface extends ExtensibleDataInterface
{
    /**
     * Get widget title
     *
     * @return string
     * @since 102.0.0
     */
    public function getWidgetTitle();

    /**
     * Set widget title
     *
     * @param string $widgetTitle
     * @return $this
     */
    public function setWidgetTitle($widgetTitle);

    /**
     * Get widget products num
     *
     * @return int
     * @since 102.0.0
     */
    public function getWidgetProductsNum();

    /**
     * Set widget products num
     *
     * @param int $widgetProductsNum
     * @return $this
     */
    public function setWidgetProductsNum($widgetProductsNum);

    /**
     * Get widget widget
     *
     * @return int
     * @since 102.0.0
     */
    public function getWidgetWidth();

    /**
     * Set widget width
     *
     * @param int $widgetWidth
     * @return $this
     */
    public function setWidgetWidth($widgetWidth);

    /**
     * Get widget height
     *
     * @return int
     * @since 102.0.0
     */
    public function getWidgetHeight();

    /**
     * Set widget height
     *
     * @param int $widgetHeight
     * @return $this
     */
    public function setWidgetHeight($widgetHeight);

    /**
     * Get widget type
     *
     * @return string
     * @since 102.0.0
     */
    public function getWidgetType();

    /**
     * Set widget type
     *
     * @param string $widgetType
     * @return $this
     */
    public function setWidgetType($widgetType);

    /**
     * Get widget show name
     *
     * @return bool
     * @since 102.0.0
     */
    public function getWidgetShowName();

    /**
     * Set widget show name
     *
     * @param bool $showName
     * @return $this
     */
    public function setWidgetShowName($showName);

    /**
     * Get widget show price
     *
     * @return bool
     * @since 102.0.0
     */
    public function getWidgetShowPrice();

    /**
     * Set widget show price
     *
     * @param bool $showPrice
     * @return $this
     */
    public function setWidgetShowPrice($showPrice);
}
