<?php

namespace Magenest\MapList\Model\ResourceModel;

use Magenest\MapList\Helper\Constant;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class HolidayLocation extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(Constant::HOLIDAY_LOCATION_TABLE, Constant::HOLIDAY_LOCATION_TABLE_ID);
    }
}
