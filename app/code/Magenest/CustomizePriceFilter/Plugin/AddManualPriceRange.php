<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 22/11/2021
 * Time: 11:41
 */

namespace Magenest\CustomizePriceFilter\Plugin;


class AddManualPriceRange
{
    public function afterToOptionArray($subject, $result)
    {
        $result[] = [
            'value' => 'manual_price_range',
            'label' => __('Manual Price Range')
        ];
        return $result;
    }
}
