<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Ward extends AbstractDb
{
    public function getDefaultNameById($wardId)
    {
        $select = $this->getConnection()->select();
        try {
            $select->from($this->getMainTable(), 'default_name');
            $select->where("ward_id = :ward_id");
            $result = $this->getConnection()->fetchOne($select, ['ward_id' => $wardId]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result = "";
        }

        return $result ?: "";
    }

    /**
     * @param $wardId
     * @return string
     */
    public function getShortNameById($wardId)
    {
        try {
            $select = $this->getConnection()->select()
                ->from($this->getMainTable(), 'name')
                ->where("ward_id = :ward_id");
            $result = $this->getConnection()->fetchOne($select, ['ward_id' => $wardId]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result = "";
        }

        return $result ?: "";
    }

    public function getWardCodeWithId($wardId)
    {
        $select = $this->getConnection()->select();
        $select->from($this->getMainTable(), 'code');
        $select->where('ward_id = :ward_id');
        $code = $this->getConnection()->fetchOne($select, ['ward_id' => $wardId]);

        return $code ?: $wardId;
    }
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('directory_ward_entity', 'ward_id');
    }

    /**
     * Create multiple
     *
     * @param array $wards
     * @param array $districts
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createMultiple($wards = [], array $districts = [])
    {
        $resultData = [];

        foreach ($wards as $ward) {
            $resultData[] = [
                'district_id' => $districts[$ward['parent_code']],
                'code' => $ward['code'],
                'name' => $ward['name'] ?? "",
                'default_name' => $ward['name_with_type'] ?? ""
            ];
        }

        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $resultData);
        $query = $this->getConnection()->select()->from(['e' => $this->getMainTable()], ['code', 'ward_id']);

        return $this->getConnection()->fetchAll($query);
    }
}
