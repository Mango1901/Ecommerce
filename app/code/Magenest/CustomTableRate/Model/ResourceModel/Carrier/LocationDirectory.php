<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model\ResourceModel\Carrier;

use Magenest\Directory\Model\ResourceModel\City\CollectionFactory as CityCollectionFactory;
use Magenest\Directory\Model\ResourceModel\District\CollectionFactory as DistrictCollectionFactory;

/**
 * Class LocationDirectory
 * @package Magenest\CustomTableRate\Model\ResourceModel\Carrier
 */
class LocationDirectory
{
    /**
     * @var array|null
     */
    protected $_cities = null;

    /**
     * @var array|null
     */
    protected $_citiesByCode = null;

    /**
     * @var CityCollectionFactory
     */
    protected $_cityCollectionFactory;

    /**
     * @var DistrictCollectionFactory
     */
    protected $_districtCollectionFactory;

    /**
     * Constructor.
     *
     * @param CityCollectionFactory $cityCollectionFactory
     * @param DistrictCollectionFactory $districtCollectionFactory
     */
    public function __construct(
        CityCollectionFactory $cityCollectionFactory,
        DistrictCollectionFactory $districtCollectionFactory
    )
    {
        $this->_cityCollectionFactory = $cityCollectionFactory;
        $this->_districtCollectionFactory = $districtCollectionFactory;
    }

    /**
     * Retrieve district id.
     *
     * @param $districtCode
     * @param $cityCode
     * @return array
     */
    public function getDistrictByCode($districtCode, $cityCode)
    {
        $this->_loadDirectory();

        return $this->_citiesByCode[$cityCode]['districts'][$districtCode];
    }

    /**
     * Check if there is district id with provided district.
     *
     * @param string $districtCode
     * @param string $cityCode
     * @return bool
     */
    public function hasDistrictCode($districtCode, $cityCode)
    {
        $this->_loadDirectory();

        return isset($this->_citiesByCode[$cityCode]['districts'][$districtCode]);
    }

    /**
     * Retrieve city id.
     *
     * @param string $cityCode
     * @return array
     */
    public function getCityByCode($cityCode)
    {
        $this->_loadDirectory();

        return $this->_citiesByCode[$cityCode];
    }

    /**
     * Check if there is city id with provided phone region code.
     *
     * @param string $cityCode
     * @return bool
     */
    public function hasCityCode($cityCode)
    {
        $this->_loadDirectory();

        return isset($this->_citiesByCode[$cityCode]);
    }

    /**
     * Load directory
     *
     * @return $this
     */
    protected function _loadDirectory()
    {
        if ($this->_cities && $this->_citiesByCode) {
            return $this;
        }

        $this->_cities = [];
        $this->_citiesByCode = [];

        /** @var \Magenest\Directory\Model\District $district */
        foreach ($this->_districtCollectionFactory->create() as $district) {
            $this->_cities[$district->getCityId()][$district->getCode()] = $district->getData();
        }

        /** @var \Magenest\Directory\Model\City $city */
        foreach ($this->_cityCollectionFactory->create() as $city) {
            $this->_citiesByCode[$city->getCode()] = [
                'city_id' => $city->getId(),
                'districts' => $this->_cities[$city->getId()] ?? []
            ];
        }

        return $this;
    }

    /**
     * Prepare string
     *
     * @param string $str
     * @return string
     */
    protected function prepareString($str)
    {
        return htmlspecialchars_decode(preg_replace('/\s+/', '', strtolower($str)), ENT_QUOTES);
    }
}
