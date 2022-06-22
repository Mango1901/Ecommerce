<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Widget;

use Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class ProductSlider
 * @package Magenest\MobileApi\Model\Catalog\Widget
 */
class ProductSlider extends AbstractSimpleObject implements ProductSliderInterface
{
    /** @const */
    const KEY_TITLE = 'title';
    const KEY_DESCRIPTION = 'description';
    const KEY_ITEMS = 'items';
    const KEY_ADDITIONAL = 'additional';
    const KEY_TOTAL_COUNT = 'total_count';
    const KEY_SHOW_MORE_URL = 'show_more_url';
    const KEY_CATEGORY_ID = 'category_id';
    const KEY_BANNERS = 'banners';
    const KEY_TYPE = 'type';

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_get(self::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::KEY_TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->_get(self::KEY_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($title)
    {
        return $this->setData(self::KEY_DESCRIPTION, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditional()
    {
        return $this->_get(self::KEY_ADDITIONAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditional($additional)
    {
        return $this->setData(self::KEY_ADDITIONAL, $additional);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->_get(self::KEY_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items)
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->_get(self::KEY_TOTAL_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCount($totalCount)
    {
        return $this->setData(self::KEY_TOTAL_COUNT, $totalCount);
    }

    /**
     * {@inheritdoc}
     */
    public function getShowMoreUrl()
    {
        return $this->_get(self::KEY_SHOW_MORE_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setShowMoreUrl($url)
    {
        return $this->setData(self::KEY_SHOW_MORE_URL, $url);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId()
    {
        return $this->_get(self::KEY_CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::KEY_CATEGORY_ID, $categoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getBanners()
    {
        return $this->_get(self::KEY_BANNERS);
    }

    /**
     * {@inheritdoc}
     */
    public function setBanners($banner)
    {
        return $this->setData(self::KEY_BANNERS, $banner);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_get(self::KEY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::KEY_TYPE, $type);
    }
}