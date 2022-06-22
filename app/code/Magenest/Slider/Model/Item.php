<?php

namespace Magenest\Slider\Model;

class Item extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(ResourceModel\Item::class);
    }
}
