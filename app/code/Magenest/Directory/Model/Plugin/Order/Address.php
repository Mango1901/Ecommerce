<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model\Plugin\Order;

/**
 * Class Address
 * @package Magenest\Directory\Model\Plugin\Order
 */
class Address
{
    /**
     * Before set custom attribute
     *
     * @param \Magento\Sales\Model\Order\Address $subject
     * @param $attributeCode
     * @param $attributeValue
     * @return array
     */
    public function beforeSetCustomAttribute(\Magento\Sales\Model\Order\Address $subject, $attributeCode, $attributeValue)
    {
        $subject->setData($attributeCode, $attributeValue);

        return [$attributeCode, $attributeValue];
    }
}
