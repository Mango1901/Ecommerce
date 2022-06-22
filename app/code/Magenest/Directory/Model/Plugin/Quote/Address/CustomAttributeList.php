<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model\Plugin\Quote\Address;

/**
 * Class CustomAttributeList
 * @package Magenest\Directory\Model\Plugin\Quote\Address
 */
class CustomAttributeList
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
            ['attribute_code' => 'city_id', 'value' => true],
            ['attribute_code' => 'district', 'value' => true],
            ['attribute_code' => 'district_id', 'value' => true],
            ['attribute_code' => 'ward', 'value' => true],
            ['attribute_code' => 'ward_id', 'value' => true ],
        ]);
    }
}
