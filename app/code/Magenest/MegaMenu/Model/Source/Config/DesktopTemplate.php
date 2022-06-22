<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_SmartNet extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_SmartNet
 */

namespace Magenest\MegaMenu\Model\Source\Config;

use Magenest\MegaMenu\Model\Source\AbstractSource;

class DesktopTemplate extends AbstractSource
{
    public static function getAllOptions()
    {
        return [
            'vertical_left' => __('Vertical Menu Left'),
            'vertical_right' => __('Vertical Menu Right'),
            'horizontal' => __('Horizontal Menu')
        ];
    }
}
