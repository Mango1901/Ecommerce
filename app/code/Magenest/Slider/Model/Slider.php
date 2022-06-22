<?php

namespace Magenest\Slider\Model;

class Slider extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'magenest_slider';

    public function _construct()
    {
        $this->_init(ResourceModel\Slider::class);
    }
}
