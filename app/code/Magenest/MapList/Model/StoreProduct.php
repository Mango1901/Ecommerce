<?php

namespace Magenest\MapList\Model;

use Magento\Framework\Model\AbstractModel;
use Magenest\MapList\Helper\Constant;

class StoreProduct extends AbstractModel
{
    protected $_idFieldName = 'store_product_id';

    protected function _construct()
    {
        $this->_init(Constant::STORE_PRODUCT_RESOURCE_MODEL);
    }
}
