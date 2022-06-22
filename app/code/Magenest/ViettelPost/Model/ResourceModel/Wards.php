<?php
namespace Magenest\ViettelPost\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magenest\ViettelPost\Model\Wards as WardsModel;

class Wards extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(WardsModel::TABLE_NAME, WardsModel::PRIMARY_KEY);
    }

    /**
     * @param $name
     * @param $districtId
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function searchWardCode($name, $districtId)
    {
        $select = $this->getConnection()->select()
            ->from(["main_table" => $this->getMainTable()], ['wards_id'])
            ->where("`wards_name` LIKE \"%$name%\" AND `district_id` = $districtId");
        return $this->getConnection()->fetchOne($select);
    }
}
