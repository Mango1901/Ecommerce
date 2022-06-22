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

class MobileTemplate extends AbstractSource
{
    /**
     * @return array
     */
    public static function getAllOptions()
    {
        return [
            0 => __('Off Canvas Left'),
            1 => __('Accordion Menu'),
            2 => __('Custom Menu Alias'),
            3 => __('Drill Down Menu')
        ];
    }
}