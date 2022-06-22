<?php

namespace Magenest\SellOnInstagram\Model\ResourceModel\Mapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\SellOnInstagram\Model\ResourceModel\Mapping
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init('Magenest\SellOnInstagram\Model\Mapping', 'Magenest\SellOnInstagram\Model\ResourceModel\Mapping');
    }

    /**
     * Get collection data as options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('id', 'name');
    }
}
