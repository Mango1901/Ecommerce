<?php
namespace Magenest\ViettelPost\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magenest\ViettelPost\Model\District as DistrictModel;

class District extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(DistrictModel::TABLE_NAME, DistrictModel::PRIMARY_KEY);
    }

    /**
     * @param $name
     * @param $cityId
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function searchDistrictCode($name, $cityId)
    {
        $select = $this->getConnection()->select()
            ->from(["main_table" => $this->getMainTable()], ['district_id'])
            ->where("`district_name` LIKE \"%$name%\" AND `province_id` = $cityId");
        return $this->getConnection()->fetchOne($select);
    }
}
