<?php

namespace Magenest\SellOnInstagram\Model\ResourceModel\InstagramProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init("Magenest\SellOnInstagram\Model\InstagramProduct", "Magenest\SellOnInstagram\Model\ResourceModel\InstagramProduct");
    }
}
