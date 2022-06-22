<?php

namespace Magenest\MapList\Model;

use Magento\Framework\Model\AbstractModel;
use Magenest\MapList\Helper\Constant;

class Brand extends AbstractModel
{
    protected $_idFieldName = 'brand_id';

    protected function _construct()
    {
        $this->_init(Constant::BRAND_RESOURCE_MODEL);
    }
}
