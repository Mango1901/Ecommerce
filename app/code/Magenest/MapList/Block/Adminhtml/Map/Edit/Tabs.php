<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 16/09/2016
 * Time: 10:17
 */

namespace Magenest\MapList\Block\Adminhtml\Map\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('map_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Map Configurations'));
    }
}
