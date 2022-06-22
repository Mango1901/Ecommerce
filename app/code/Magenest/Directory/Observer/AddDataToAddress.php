<?php

namespace Magenest\Directory\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddDataToAddress
 * @package Magenest\Directory\Observer
 */
class AddDataToAddress implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $address = $observer->getShippingAssignment()->getShipping()->getAddress();
        if (!empty($data)) {
            $address->setData($data);
        }
        $customAttr = ['city_id', 'district_id', 'ward_id'];
        foreach ($customAttr as $attr) {
            if ($address->getCustomAttribute($attr)) {
                $address->setData($attr, preg_replace("/[^0-9]/", "", $address->getCustomAttribute($attr)->getValue()));
            }
        }
    }
}
