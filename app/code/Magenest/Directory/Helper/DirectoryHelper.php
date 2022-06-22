<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kootoro extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kootoro
 */

namespace Magenest\Directory\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ResourceConnection;

class DirectoryHelper extends AbstractHelper
{
    protected $wardResource;

    protected $districtResource;

    protected $cityResource;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * DirectoryHelper constructor.
     *
     * @param Context $context
     * @param \Magenest\Directory\Model\ResourceModel\City $cityResource
     * @param \Magenest\Directory\Model\ResourceModel\District $districtResource
     * @param \Magenest\Directory\Model\ResourceModel\Ward $wardResource
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        \Magenest\Directory\Model\ResourceModel\City $cityResource,
        \Magenest\Directory\Model\ResourceModel\District $districtResource,
        \Magenest\Directory\Model\ResourceModel\Ward $wardResource,
        ResourceConnection $resourceConnection
    ) {
        $this->wardResource = $wardResource;
        $this->districtResource = $districtResource;
        $this->cityResource = $cityResource;
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
    }

    public function getCityDefaultName($cityId)
    {
        return $this->cityResource->getDefaultNameById($cityId);
    }

    public function getDistrictDefaultName($districtId)
    {
        return $this->districtResource->getDefaultNameById($districtId);
    }

    public function getWardDefaultName($wardId)
    {
        return $this->wardResource->getDefaultNameById($wardId);
    }

    public function validateCityBeforeDelete($ids)
    {
        if ($this->isIdsExist('customer_address_entity','city_id',$ids) &&
            $this->isIdsExist('sales_order_address','city_id',$ids)) {
            return false;
        }
        return true;
    }
    public function validateDistrictBeforeDelete($ids)
    {
        if ($this->isIdsExist('customer_address_entity','district_id',$ids) &&
            $this->isIdsExist('sales_order_address','district_id',$ids)) {
            return false;
        }
        return true;
    }
    public function validateWardBeforeDelete($ids)
    {
        if ($this->isIdsExist('customer_address_entity','ward_id',$ids) &&
            $this->isIdsExist('sales_order_address','ward_id',$ids)) {
            return false;
        }
        return true;
    }
    public function isIdsExist($tableName,$fieldName,$ids)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName($tableName))
            ->where("$fieldName IN (?)", $ids);

        return $connection->fetchAll($select);
    }

    public function isDisableOnStoreFront($table,$column,$id)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            ['main_table' => $this->resourceConnection->getTableName($table)],
            ['main_table.disable_on_storefront']
        )->where("$column = ?",$id);
        $result = (bool) $connection->fetchOne($select);

        return $result ? [1 => __('Disabled')] : [0 => __('Enabled')];
    }
    public function isDisableDistrictOnStoreFront($table,$column,$id)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            ['main_table' => $this->resourceConnection->getTableName($table)],
            ['district' =>'main_table.disable_on_storefront','city' => 'dce.disable_on_storefront']
        )->join(
            ['dce' => $this->resourceConnection->getTableName('directory_city_entity')],
            'main_table.city_id = dce.city_id'
        )->where("main_table.$column = ?",$id);

        $result = $connection->fetchRow($select);

        if (!empty($result['district'] || !empty($result['city']))) {
            return [1 => __('Disabled')];
        }
        return [0 => __('Enabled')];
    }
}
