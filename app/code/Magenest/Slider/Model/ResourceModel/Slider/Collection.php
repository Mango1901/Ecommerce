<?php

namespace Magenest\Slider\Model\ResourceModel\Slider;

/**
 * Class Collection
 * @package Magenest\Slider\Model\ResourceModel\Slider
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'slider_id';
    /**
     *  Initialize  resource    collection
     *
     *  @return     void
     */
    public function _construct()
    {
        $this->_init(\Magenest\Slider\Model\Slider::class, \Magenest\Slider\Model\ResourceModel\Slider::class);
    }
}
