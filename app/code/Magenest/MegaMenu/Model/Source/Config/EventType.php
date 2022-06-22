<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Model\Source\Config;

use Magenest\MegaMenu\Model\Source\AbstractSource;

class EventType extends AbstractSource
{

    /**
     * @return array
     */
    public static function getAllOptions()
    {
        return [
            'hover' => __('Hover'),
            'click' => __('Click')
        ];
    }
}