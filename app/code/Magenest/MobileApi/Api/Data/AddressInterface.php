<?php

namespace Magenest\MobileApi\Api\Data;

interface AddressInterface extends \Magento\Customer\Api\Data\AddressInterface
{
    /** @var string */
    const CITY_ID = "city_id";
    /** @var string */
    const DISTRICT_ID = "district_id";
    /** @var string */
    const WARD_ID = "ward_id";
    /** @var string */
    const DISTRICT = "district";
    /** @var string */
    const WARD = "ward";

    /**
     * Get city id
     *
     * @return int|null
     */
    public function getCityId();

    /**
     * Set city id
     *
     * @param $cityId
     * @return $this
     */
    public function setCityId($cityId);

    /**
     * Get district id
     *
     * @return int|null
     */
    public function getDistrictId();

    /**
     * Set district id
     *
     * @param $districtId
     * @return $this
     */
    public function setDistrictId($districtId);

    /**
     * Get ward id
     *
     * @return int|null
     */
    public function getWardId();

    /**
     * Set ward id
     *
     * @param $wardId
     * @return $this
     */
    public function setWardId($wardId);

    /**
     * Get district
     *
     * @return string|null
     */
    public function getDistrict();

    /**
     * Set district
     *
     * @param string $district
     * @return $this
     */
    public function setDistrict($district);

    /**
     * Get ward
     *
     * @return string|null
     */
    public function getWard();

    /**
     * Set ward
     *
     * @param string $ward
     * @return $this
     */
    public function setWard($ward);
}
