<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Promotion;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Promotion\GiftInterface;

/**
 * Class Gift
 * @package Magenest\MobileApi\Model\Promotion
 */
class Gift extends AbstractSimpleObject implements GiftInterface
{
    /** @const */
    const KEY_ICON = 'icon';
    const KEY_TITLE = 'title';
    const KEY_ITEMS = 'items';

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->_get(self::KEY_ICON);
    }

    /**
     * {@inheritdoc}
     */
    public function setIcon($icon)
    {
        return $this->setData(self::KEY_ICON, $icon);
    }

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
    public function getItems()
    {
        return $this->_get(self::KEY_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }
}
