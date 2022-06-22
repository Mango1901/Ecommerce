<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Affiliate;

use Magenest\MobileApi\Api\Data\Affiliate\WidgetInterface;
use Magento\Framework\DataObject;

/**
 * Class Widget
 * @package Magenest\MobileApi\Model\Affiliate
 */
class Widget extends DataObject implements WidgetInterface
{
    /** @const */
    const KEY_WIDGET_TITLE = 'widget_title';
    const KEY_WIDGET_PRODUCTS_NUM = 'widget_products_num';
    const KEY_WIDGET_WIDTH = 'widget_width';
    const KEY_WIDGET_HEIGHT = 'widget_height';
    const KEY_WIDGET_TYPE = 'widget_type';
    const KEY_WIDGET_SHOW_NAME = 'widget_show_name';
    const KEY_WIDGET_SHOW_PRICE = 'widget_show_price';

    /**
     * @inheritdoc
     */
    public function getWidgetTitle()
    {
        return $this->getData(self::KEY_WIDGET_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setWidgetTitle($widgetTitle)
    {
        $this->setData(self::KEY_WIDGET_TITLE, $widgetTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidgetProductsNum()
    {
        $this->getData(self::KEY_WIDGET_PRODUCTS_NUM);
    }

    /**
     * @inheritdoc
     */
    public function setWidgetProductsNum($widgetProductsNum)
    {
        $this->setData(self::KEY_WIDGET_PRODUCTS_NUM, $widgetProductsNum);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidgetWidth()
    {
        return $this->getData(self::KEY_WIDGET_WIDTH);
    }

    /**
     * @inheritdoc
     */
    public function setWidgetWidth($widgetWidth)
    {
        $this->setData(self::KEY_WIDGET_WIDTH, $widgetWidth);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidgetHeight()
    {
        return $this->getData(self::KEY_WIDGET_HEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setWidgetHeight($widgetHeight)
    {
        $this->setData(self::KEY_WIDGET_HEIGHT, $widgetHeight);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidgetType()
    {
        return $this->getData(self::KEY_WIDGET_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setWidgetType($widgetType)
    {
        $this->setData(self::KEY_WIDGET_TYPE, $widgetType);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidgetShowName()
    {
        return $this->getData(self::KEY_WIDGET_SHOW_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setWidgetShowName($showName)
    {
        $this->setData(self::KEY_WIDGET_SHOW_NAME, $showName);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidgetShowPrice()
    {
        return $this->getData(self::KEY_WIDGET_SHOW_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setWidgetShowPrice($showPrice)
    {
        $this->setData(self::KEY_WIDGET_SHOW_PRICE, $showPrice);

        return $this;
    }
}