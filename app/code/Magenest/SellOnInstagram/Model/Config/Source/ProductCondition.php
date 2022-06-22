<?php

namespace Magenest\SellOnInstagram\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ProductCondition implements ArrayInterface
{
    const NEW = 'new';
    const REFURBISHED = 'refurbished';
    const USED = 'used';
    const USED_LIKE_NEW = 'used_like_new';
    const USED_GOOD = 'used_good';
    const USED_FAIR = 'used_fair';
    const CPO = 'cpo';
    const OPEN_BOX_NEW = 'open_box_new';
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NEW, 'label' => __('New')],
            ['value' => self::REFURBISHED, 'label' => __('Refurbished')],
            ['value' => self::USED, 'label' => __('Used')],
            ['value' => self::USED_LIKE_NEW, 'label' => __('Used like new')],
            ['value' => self::USED_GOOD, 'label' => __('Used good')],
            ['value' => self::USED_FAIR, 'label' => __('Used fair')],
            ['value' => self::CPO, 'label' => __('CPO')],
            ['value' => self::OPEN_BOX_NEW, 'label' => __('Open box new')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::NEW => __('New'),
            self::REFURBISHED => __('Refurbished'),
            self::USED => __('Used'),
            self::USED_LIKE_NEW => __('Used like new'),
            self::USED_GOOD => __('Used good'),
            self::USED_FAIR => __('Used fair'),
            self::CPO => __('CPO'),
            self::OPEN_BOX_NEW => __('Open box new'),

        ];
    }
}
