<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model\ResourceModel\Carrier;

use Magenest\CustomTableRate\Model\KangarooTableRates;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\CustomTableRate\Model\ResourceModel\Carrier
 */
class Collection extends AbstractCollection
{
    /**
     * Directory/country table name
     *
     * @var string
     */
    protected $_countryTable;

    /**
     * Directory/country_region table name
     *
     * @var string
     */
    protected $_regionTable;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Magenest\CustomTableRate\Model\TableRates', 'Magenest\CustomTableRate\Model\ResourceModel\Carrier');
        $this->_countryTable = $this->getTable('directory_country');
        $this->_regionTable = $this->getTable('directory_country_region');
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(
                ['district' => $this->getTable('directory_district_entity')],
                'district.code = main_table.district_code',
                ['district_name' => 'district.default_name']
            )->join(
                ['city' => $this->getTable('directory_city_entity')],
                'city.code = main_table.city_code',
                ['city_name' => 'city.default_name']
            );

        return $this;
    }

    /**
     * Add website filter to collection
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteFilter($websiteId)
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * Add country filter to collection
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryFilter($countryId)
    {
        return $this->addFieldToFilter('country_id', $countryId);
    }
}
