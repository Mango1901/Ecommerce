<?php
namespace Magenest\Directory\Model\Plugin\Customer\Model;

use Magento\Customer\Api\Data\AddressInterface;

class Address
{
    public function afterUpdateData(\Magento\Customer\Model\Address $subject, $result, AddressInterface $address)
    {
       $data = $address->__toArray();
       if (isset($data['city_id'], $data['district_id'], $data['ward_id'])) {
           $result->setCity($data['city'] ?? '');
           $result->setCityId($data['city_id'] ?? '');
           $result->setDistrict($data['district'] ?? '');
           $result->setDistrictId($data['district_id'] ?? '');
           $result->setWardId($data['ward_id'] ?? '');
           $result->setWard($data['ward'] ?? '');
       }
       return $result;
    }
}
