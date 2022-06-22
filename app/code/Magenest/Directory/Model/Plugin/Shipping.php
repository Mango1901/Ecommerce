<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Model\Plugin;

use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\Api\AttributeValue;
use Magenest\Directory\Model\Plugin\Quote\Address;

/**
 * Class Shipping
 * @package Magenest\Directory\Model\Plugin
 */
class Shipping
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Constructor.
     *
     * @param Registry $registry
     */
    function __construct(
        Registry $registry
    )
    {
        $this->_registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeCollectRates(\Magento\Shipping\Model\Shipping $subject, RateRequest $request)
    {
        $customAttributes = $this->_registry->registry(Address::CUSTOM_ATTRIBUTE_KEY);
        if (!is_null($customAttributes)) {
            foreach ($customAttributes as $attribute) {
                if($attribute instanceof AttributeValue){
                    $attribute = $attribute->__toArray();
                }

                if (in_array($attribute['attribute_code'], ['city_id', 'district', 'district_id', 'ward', 'ward_id'])) {
                    if (is_array($attribute['value']) && count($attribute['value']) == 2) {
                        $request->setData('dest_' . $attribute['attribute_code'], end($attribute['value']));
                    } else {
                        $request->setData('dest_' . $attribute['attribute_code'], $attribute['value']);
                    }
                }
            }

            $request->setData('custom_attributes', $customAttributes);
        }

        return [$request];
    }
}
