<?php

namespace Magenest\MapList\Model;

use Magento\Framework\Model\AbstractModel;
use Magenest\MapList\Helper\Constant;

class HolidayLocation extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(Constant::HOLIDAY_LOCATION_RESOURCE_MODEL);
    }
}
