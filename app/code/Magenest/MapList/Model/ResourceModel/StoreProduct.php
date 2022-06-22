<?php

namespace Magenest\MapList\Model\ResourceModel;

use Magenest\MapList\Helper\Constant;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StoreProduct extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(Constant::STORE_PRODUCT_TABLE, Constant::STORE_PRODUCT_TABLE_ID);
    }
}
