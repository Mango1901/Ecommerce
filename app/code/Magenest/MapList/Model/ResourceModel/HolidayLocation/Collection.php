<?php


namespace Magenest\MapList\Model\ResourceModel\HolidayLocation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magenest\MapList\Helper\Constant;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'holiday_location_id';

    public function _construct()
    {
        $this->_init(Constant::HOLIDAY_LOCATION_MODEL, Constant::HOLIDAY_LOCATION_RESOURCE_MODEL);
    }
}
