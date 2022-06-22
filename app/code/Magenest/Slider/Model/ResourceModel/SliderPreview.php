<?php

namespace   Magenest\Slider\Model\ResourceModel;

/**
 * Class Button
 * @package Magenest\CustomerAttributes\Model\ResourceModel
 */
class SliderPreview extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_slider_preview', 'preview_id');
    }
}
