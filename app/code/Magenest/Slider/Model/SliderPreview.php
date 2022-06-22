<?php

namespace Magenest\Slider\Model;

/**
 * Class Button
 * @package Magenest\Slider\Model
 */
class SliderPreview extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(ResourceModel\SliderPreview::class);
    }
}
