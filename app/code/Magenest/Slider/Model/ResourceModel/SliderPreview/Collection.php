<?php

namespace Magenest\Slider\Model\ResourceModel\SliderPreview;

/**
 * Class Collection
 * @package Magenest\Slider\Model\ResourceModel\Button
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'preview_id';
    /**
     *  Initialize resource collection
     *
     *  @return void
     */
    public function _construct()
    {
        $this->_init(\Magenest\Slider\Model\SliderPreview::class, \Magenest\Slider\Model\ResourceModel\SliderPreview::class);
    }
}
