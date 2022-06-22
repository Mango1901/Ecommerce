<?php


namespace Magenest\MapList\Model\ResourceModel\Brand;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magenest\MapList\Helper\Constant;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'brand_id';

    public function _construct()
    {
        $this->_init(Constant::BRAND_MODEL, Constant::BRAND_RESOURCE_MODEL);
    }
}
