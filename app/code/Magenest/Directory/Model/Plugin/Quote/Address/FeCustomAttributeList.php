<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 12/11/2021
 * Time: 09:08
 */

namespace Magenest\Directory\Model\Plugin\Quote\Address;


class FeCustomAttributeList
{
    /**
     * After get attributes
     *
     * @param \Magento\Quote\Model\Quote\Address\CustomAttributeList $subject
     * @param $result
     * @return array
     */
    public function afterGetAttributes(\Magento\Quote\Model\Quote\Address\CustomAttributeList $subject, $result)
    {
        return array_merge($result, [
            'city_id' => true,
            'district' => true,
            'district_id' => true,
            'ward' => true,
            'ward_id' => true,
        ]);
    }
}

