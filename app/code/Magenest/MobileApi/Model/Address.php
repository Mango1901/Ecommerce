<?php

namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Api\Data\AddressInterface;

class Address extends \Magento\Customer\Model\Data\Address implements AddressInterface
{
    /**
     * Get city ID
     *
     * @return int|mixed|null
     */
    public function getCityId()
    {
        return $this->_get(self::CITY_ID);
    }

    /**
     * Set city ID
     *
     * @param $cityId
     * @return Address
     */
    public function setCityId($cityId)
    {
        return $this->setData(self::CITY_ID, $cityId);
    }

    /**
     * Get district ID
     *
     * @return int|mixed|null
     */
    public function getDistrictId()
    {
        return $this->_get(self::DISTRICT_ID);
    }

    /**
     * Set district ID
     *
     * @param $districtId
     * @return Address
     */
    public function setDistrictId($districtId)
    {
        return $this->setData(self::DISTRICT_ID, $districtId);
    }

    /**
     * Get ward ID
     *
     * @return int|mixed|null
     */
    public function getWardId()
    {
        return $this->_get(self::WARD_ID);
    }

    /**
     * Set Ward ID
     *
     * @param $wardId
     * @return Address
     */
    public function setWardId($wardId)
    {
        return $this->setData(self::WARD_ID, $wardId);
    }

    /**
     * Get district
     *
     * @return mixed|string|null
     */
    public function getDistrict()
    {
        return $this->_get(self::DISTRICT);
    }

    /**
     * Set district
     *
     * @param string $district
     * @return Address
     */
    public function setDistrict($district)
    {
        return $this->setData(self::DISTRICT, $district);
    }

    /**
     * Get ward
     *
     * @return mixed|string|null
     */
    public function getWard()
    {
        return $this->_get(self::WARD);
    }

    /**
     * Set ward
     *
     * @param string $ward
     * @return Address
     */
    public function setWard($ward)
    {
        return $this->setData(self::WARD, $ward);
    }
}
