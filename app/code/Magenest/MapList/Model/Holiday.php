<?php

namespace Magenest\MapList\Model;

use Magento\Framework\Model\AbstractModel;
use Magenest\MapList\Helper\Constant;

class Holiday extends AbstractModel
{
    protected $_idFieldName = 'holiday_id';

    protected function _construct()
    {
        $this->_init(Constant::HOLIDAY_RESOURCE_MODEL);
    }
}
