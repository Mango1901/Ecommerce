<?php

namespace Magenest\MobileApi\Api;

interface UserManagementInterface
{
    /**
     * Add customer address
     *
     * @param int $customerId
     * @param \Magenest\MobileApi\Api\Data\AddressInterface $address
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addDeliveryAddress($customerId, $address);

    /**
     * Get VN available city
     *
     * @param string $countryId Country code, default is 'vn'
     * @return string[]
     */
    public function getVNAvailableLocations($countryId = 'vn');

    /**
     * Get VN district by city
     *
     * @param int $cityId
     * @return string[]
     */
    public function getVNDistrictsByCity($cityId);

    /**
     * Get VN ward by district
     *
     * @param int $districtId
     * @return string[]
     */
    public function getVNWardsByDistrict($districtId);
}
