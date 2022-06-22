<?php

namespace Magenest\Slider\Model\ResourceModel;

/**
 * Class Item
 * @package Magenest\Slider\Model\ResourceModel
 */
class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_slider_item_entity', 'item_id');
    }
}
