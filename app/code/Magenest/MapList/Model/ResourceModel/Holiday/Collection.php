<?php


namespace Magenest\MapList\Model\ResourceModel\Holiday;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magenest\MapList\Helper\Constant;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'holiday_id';

    public function _construct()
    {
        $this->_init(Constant::HOLIDAY_MODEL, Constant::HOLIDAY_RESOURCE_MODEL);
    }
}
