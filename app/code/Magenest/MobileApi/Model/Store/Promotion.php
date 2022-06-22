<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Store;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Store\PromotionInterface;

/**
 * Class Promotion
 * @package Magenest\MobileApi\Model\Store
 */
class Promotion extends AbstractSimpleObject implements PromotionInterface
{
    /** @const */
    const KEY_BANNER = 'banner';
    const KEY_PANELS = 'panels';

    /**
     * {@inheritdoc}
     */
    public function getBanner()
    {
        return $this->_get(self::KEY_BANNER);
    }

    /**
     * {@inheritdoc}
     */
    public function setBanner($banner)
    {
        return $this->setData(self::KEY_BANNER, $banner);
    }

    /**
     * {@inheritdoc}
     */
    public function getPanels()
    {
        return $this->_get(self::KEY_PANELS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPanels($panels)
    {
        return $this->setData(self::KEY_PANELS, $panels);
    }
}
