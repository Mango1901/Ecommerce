<?php

namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Api\Data\AddressInterface;
use Magenest\MobileApi\Api\UserManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magenest\Directory\Model\ResourceModel\City\CollectionFactory as CityCollection;
use Magenest\Directory\Model\ResourceModel\District\CollectionFactory as DistrictCollection;
use Magenest\Directory\Model\ResourceModel\Ward\CollectionFactory as WardCollection;

class UserManagement implements UserManagementInterface
{
    /** @var AddressRepositoryInterface */
    private $_addressRepository;

    /** @var CityCollection */
    private $_cityCollection;

    /** @var DistrictCollection */
    private $_districtCollection;

    /** @var WardCollection */
    private $_wardCollection;

    /**
     * UserManagement constructor.
     *
     * @param AddressRepositoryInterface $addressRepository
     * @param CityCollection $cityCollection
     * @param DistrictCollection $districtCollection
     * @param WardCollection $wardCollection
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        CityCollection $cityCollection,
        DistrictCollection $districtCollection,
        WardCollection $wardCollection
    ) {
        $this->_addressRepository = $addressRepository;
        $this->_cityCollection = $cityCollection;
        $this->_districtCollection = $districtCollection;
        $this->_wardCollection = $wardCollection;
    }

    /**
     * Add customer address
     *
     * @param int $customerId
     * @param AddressInterface $address
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws LocalizedException
     */
    public function addDeliveryAddress($customerId, $address)
    {
        $address->setCustomerId($customerId);
        return $this->_addressRepository->save($address);
    }

    /**
     * Get city by country id
     *
     * @param string $countryId
     * @return string[]
     */
    public function getVNAvailableLocations($countryId = 'vn')
    {
        $cities = $this->_cityCollection->create()
                                       ->addFieldToFilter('country_id', $countryId);

        return $this->formatAddressData($cities->prepareOptionArray());
    }

    /**
     * Get district by city id
     *
     * @param int $cityId
     * @return string[]
     */
    public function getVNDistrictsByCity($cityId)
    {
        $districts = $this->_districtCollection->create()->addFieldToFilter('city_id', $cityId);

        return $this->formatAddressData($districts->prepareOptionArray());
    }

    /**
     * Get wards by district id
     *
     * @param int $districtId
     * @return string[]
     */
    public function getVNWardsByDistrict($districtId)
    {
        $wards = $this->_wardCollection->create()->addFieldToFilter('district_id', $districtId);

        return $this->formatAddressData($wards->prepareOptionArray());
    }

    /**
     * Format output address
     *
     * @param array $addresses
     *
     * @return array
     */
    private function formatAddressData($addresses) {
        $data = [];
        foreach ($addresses as $address) {
            $data[ $address['value'] ]['id']           = $address['value'];
            $data[ $address['value'] ]['default_name'] = $address['label'];
            $data[ $address['value'] ]['name']         = $address['name'];
            $data[ $address['value'] ]['full_name']    = $address['full_name'];
        }

        return $data;
    }
}
