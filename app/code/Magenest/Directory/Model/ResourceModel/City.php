<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class City extends AbstractDb
{
    const MAIN_TABLE = 'directory_city_entity';
    const DISABLED_ON_STOREFRONT = 1;

    public function getDefaultNameById($cityId)
    {
        $select = $this->getConnection()->select();
        try {
            $select->from($this->getMainTable(), 'default_name');
            $select->where("city_id = :city_id");
            $result = $this->getConnection()->fetchOne($select, ['city_id' => $cityId]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result = "";
        }

        return $result ?: "";
    }

    /**
     * @param $cityId
     * @return string
     */
    public function getShortNameById($cityId)
    {
        try {
            $select = $this->getConnection()->select()
                ->from($this->getMainTable(), 'name')
                ->where("city_id = :city_id");
            $result = $this->getConnection()->fetchOne($select, ['city_id' => $cityId]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result = "";
        }

        return $result ?: "";
    }

    /**
     * Create multiple
     *
     * @param array $cities
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createMultiple($cities = [])
    {
        $resultData = [];

        foreach ($cities as $city) {
            $resultData[] = [
                'country_id' => 'VN',
                'code' => $city['code'],
                'name' => $city['name'] ?? "",
                'default_name' => $city['name_with_type'] ?? ""
            ];
        }

        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $resultData);
        $query = $this->getConnection()->select()->from(['e' => $this->getMainTable()], ['code', 'city_id']);
        $cities = [];

        foreach ($this->getConnection()->fetchAll($query) as $record) {
            $cities[$record['code']] = $record['city_id'];
        }

        return $cities;
    }

    public function getCityCodeWithId($cityId)
    {
        $select = $this->getConnection()->select();
        $select->from($this->getTable(self::MAIN_TABLE), 'code');
        $select->where('city_id = :city_id');
        $code = $this->getConnection()->fetchOne($select, ['city_id' => $cityId]);

        return $code ?: $cityId;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('directory_city_entity', 'city_id');
    }

    public function fetchAllDisableOnStoreFront()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), 'city_id')
            ->where("disable_on_storefront = ?", self::DISABLED_ON_STOREFRONT);

        return $this->getConnection()->fetchCol($select);
    }
}
