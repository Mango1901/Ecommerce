<?php

namespace Magenest\MapList\Model\ResourceModel;

use Magenest\MapList\Helper\Constant;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Holiday extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(Constant::HOLIDAY_TABLE, Constant::HOLIDAY_TABLE_ID);
    }

    public function getHoliday($locationId){
        $mainTable = $this->getMainTable();

        $holidayLocation = $this->getTable('magenest_maplist_holiday_location');

        $adapter = $this->_getConnection('read');

        $select = $adapter->select()->from(
            array('main_table' => $mainTable),
            '*'
        )
            ->join(array('holiday_location_table' => $holidayLocation), 'main_table.holiday_id = holiday_location_table.holiday_id')
            ->where('holiday_location_table.location_id=' . $locationId . ' and main_table.status = 1');

        $row = $adapter->fetchAssoc($select);

        return $row;
    }
}
