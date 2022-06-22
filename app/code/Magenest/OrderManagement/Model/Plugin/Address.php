<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderManagement\Model\Plugin;

/**
 * Class Address
 */
class Address
{
    /**
     * After get postcode
     *
     * @param \Magento\Sales\Model\Order\Address $subject
     * @param $result
     * @return string
     */
    public function afterGetPostcode(\Magento\Sales\Model\Order\Address $subject, $result)
    {
        if(!$result){
            $result = '';
        }

        return $result;
    }
}
