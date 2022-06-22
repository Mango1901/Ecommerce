<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\MegaMenu\Model\Source\Config;

use Magenest\MegaMenu\Model\Source\AbstractSource;

class MenuType extends AbstractSource
{
    const MENU_TYPE_CMS = 1;
    const MENU_TYPE_CAT = 2;
    const MENU_TYPE_CUS = 3;

    /**
     * @return array
     */
    public static function getAllOptions()
    {
        return [
            self::MENU_TYPE_CMS => __("Cms Page"),
            self::MENU_TYPE_CAT => __("Category Page"),
            self::MENU_TYPE_CUS => __("Custom Page"),
        ];
    }
}
