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

class Effect extends AbstractSource
{
    /**
     * @return array
     */
    public static function getAllOptions()
    {
        return [
            'z' => __('Zoom'),
            'btt' => __('Bottom to Top'),
            'ttb' => __('Top to Bottom'),
            'rtl' => __('Right to Left'),
            'ltr' => __('Left to Right'),
            'none' => __('None')
        ];
    }
}