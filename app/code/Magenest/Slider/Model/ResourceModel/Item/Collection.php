<?php

namespace Magenest\Slider\Model\ResourceModel\Item;

/**
 * Class Collection
 * @package Magenest\Slider\Model\ResourceModel\Item
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'item_id';
    /**
     *  Initialize  resource    collection
     *
     *  @return     void
     */
    public function _construct()
    {
        $this->_init(\Magenest\Slider\Model\Item::class, \Magenest\Slider\Model\ResourceModel\Item::class);
    }
}
