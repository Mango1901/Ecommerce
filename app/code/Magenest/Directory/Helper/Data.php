<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magenest\Directory\Model\ResourceModel\Ward\CollectionFactory as WardCollection;
use Magenest\Directory\Model\ResourceModel\City\CollectionFactory as CityCollection;
use Magenest\Directory\Model\ResourceModel\District\CollectionFactory as DistrictCollection;

/**
 * Class Data
 * @package Magenest\Directory\Helper
 */
class Data extends AbstractHelper
{
    /** @const - Cache Path */
    const CACHE_DIRECTORY_CITY_JSON_STORE = 'CACHE_DIRECTORY_CITY_JSON_STORE';
    const CACHE_TAG_CITY = 'CACHE_TAG_CITY';
    const CACHE_DIRECTORY_DISTRICT_JSON_STORE = 'CACHE_DIRECTORY_DISTRICT_JSON_STORE';
    const CACHE_TAG_DISTRICT = 'CACHE_TAG_DISTRICT';
    const CACHE_DIRECTORY_WARD_JSON_STORE = 'CACHE_DIRECTORY_WARD_JSON_STORE';
    const CACHE_TAG_WARD = 'CACHE_TAG_WARD';
    const CACHE_DIRECTORY_DATA_JSON_STORE = 'CACHE_DIRECTORY_DATA_JSON_STORE';
    const CACHE_TAG_DATA = 'CACHE_TAG_DATA';

    /**
     * Json representation of regions data
     *
     * @var string
     */
    protected $_regionJson;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Config
     */
    protected $_configCacheType;

    /**
     * @var JsonData
     */
    protected $_jsonHelper;

    /**
     * @var CityCollection
     */
    protected $_cityCollection;

    /**
     * @var DistrictCollection
     */
    protected $_districtCollection;

    /**
     * @var WardCollection
     */
    protected $_wardCollection;

    /**
     * City Options
     */
    private $_cityOptions = [];

    /**
     * District Options
     */
    private $_districtOptions = [];

    /**
     * Ward Options
     */
    private $_wardOptions = [];

    private $address;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Config $configCacheType
     * @param JsonData $jsonHelper
     * @param CityCollection $cityCollection
     * @param DistrictCollection $districtCollection
     * @param WardCollection $wardCollection
     * @param \Magento\Customer\Model\Address $address
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Config $configCacheType,
        JsonData $jsonHelper,
        CityCollection $cityCollection,
        DistrictCollection $districtCollection,
        WardCollection $wardCollection,
        \Magento\Customer\Model\Address $address,
        ResourceConnection $resourceConnection
    )
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_configCacheType = $configCacheType;
        $this->_jsonHelper = $jsonHelper;
        $this->_cityCollection = $cityCollection;
        $this->_districtCollection = $districtCollection;
        $this->_wardCollection = $wardCollection;
        $this->address = $address;
        $this->resourceConnection = $resourceConnection;
    }

    public function getAddressById($addressId)
    {
        if (!$addressId) {
            return [];
        }
        return $this->address->load($addressId);
    }

    /**
     * Get city options
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCityOptions()
    {
        if (empty($this->_cityOptions)) {
            $cacheKey = self::CACHE_DIRECTORY_CITY_JSON_STORE . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);

            if (empty($json)) {
                $cities = $this->_cityCollection->create()->storefrontToOptionArray();
                $json = $this->_jsonHelper->jsonEncode($cities);
                if ($json === false) {
                    $json = 'false';
                }

                $this->_configCacheType->save($json, $cacheKey,[self::CACHE_TAG_CITY]);
            }

            $this->_cityOptions = $this->_jsonHelper->jsonDecode($json);
        }

        return $this->_cityOptions;
    }

    /**
     * Get district options
     *
     * @return array
     */
    public function getDistrictOptions()
    {
        if (empty($this->_districtOptions)) {
            $cacheKey = self::CACHE_DIRECTORY_DISTRICT_JSON_STORE . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);

            if(empty($json)){
                $districts = $this->_districtCollection->create()->storefrontToOptionArray();
                $json = $this->_jsonHelper->jsonEncode($districts);
                if ($json === false) {
                    $json = 'false';
                }

                $this->_configCacheType->save($json, $cacheKey, [self::CACHE_TAG_DISTRICT]);
            }

            $this->_districtOptions = $this->_jsonHelper->jsonDecode($json);
        }

        return $this->_districtOptions;
    }

    /**
     * Get ward options
     *
     * @return array
     */
    public function getWardOptions()
    {
        if (empty($this->_wardOptions)) {
            $cacheKey = self::CACHE_DIRECTORY_WARD_JSON_STORE . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);

            if(empty($json)){
                $wards = $this->_wardCollection->create()->storefrontToOptionArray();
                $json = $this->_jsonHelper->jsonEncode($wards);
                if ($json === false) {
                    $json = 'false';
                }

                $this->_configCacheType->save($json, $cacheKey, [self::CACHE_TAG_WARD]);
            }

            $this->_wardOptions = $this->_jsonHelper->jsonDecode($json);
        }

        return $this->_wardOptions;
    }

    /**
     * Retrieve data json
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDataJson()
    {
        if (!$this->_regionJson) {
            $cacheKey = self::CACHE_DIRECTORY_DATA_JSON_STORE . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);

            if (empty($json)) {
                $regions = $this->getData();
                $json = $this->_jsonHelper->jsonEncode($regions);
                if ($json === false) {
                    $json = 'false';
                }

                $this->_configCacheType->save($json, $cacheKey , [self::CACHE_TAG_DATA]);
            }

            $this->_regionJson = $json;
        }

        return $this->_regionJson;
    }

    /**
     * Retrieve data
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        $cites = $this->_cityCollection->create()->addFieldToFilter('country_id', 'VN');
        $districts = $this->_districtCollection->create();
        $wards = $this->_wardCollection->create();

        foreach ($wards->storefrontToOptionArray() as $ward) {
            $data['tmp'][$ward[$wards->getForeignKey()]]['wards'][$ward['value']] =
                [
                    'id' => $ward['value'],
                    'default_name' => $ward['label'],
                    'name' => $ward['name'] ?? "",
                    'full_name' => $ward['full_name'] ?? ""
                ];
        }

        foreach ($districts->storefrontToOptionArray() as $district) {
            if (isset($district['name']) && strlen($district['name']) == 1) {
                $district['name'] = "0".$district['name'];
            }

            $data['tmp'][$district['value']]['id'] = $district['value'];
            $data['tmp'][$district['value']]['default_name'] = $district['label'];
            $data['tmp'][$district['value']]['name'] = $district['name'] ?? "";
            $data['tmp'][$district['value']]['full_name'] = $district['full_name'] ?? "";
            $data['VN'][$district[$districts->getForeignKey()]]['districts'][$district['value']] = $data['tmp'][$district['value']];
        }

        foreach ($cites->storefrontToOptionArray() as $city) {
            $data['VN'][$city['value']]['id'] = $city['value'];
            $data['VN'][$city['value']]['default_name'] = $city['label'];
            $data['VN'][$city['value']]['name'] = $city['name'] ?? "";
            $data['VN'][$city['value']]['full_name'] = $city['full_name'] ?? "";
        }

        unset($data['tmp']);

        return $data;
    }
    public function getQuoteAddressById($id)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $this->resourceConnection->getTableName('quote_address'))
            ->where('quote_id = ?', $id)
            ->where('city_id != ?','null')
        ->order('address_id DESC');
        return $connection->fetchRow($select);
    }
}
