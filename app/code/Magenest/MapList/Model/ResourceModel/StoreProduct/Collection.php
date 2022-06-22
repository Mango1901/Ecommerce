<?php


namespace Magenest\MapList\Model\ResourceModel\StoreProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magenest\MapList\Helper\Constant;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'store_product_id';

    public function _construct()
    {
        $this->_init(Constant::STORE_PRODUCT_MODEL, Constant::STORE_PRODUCT_RESOURCE_MODEL);
    }
}
