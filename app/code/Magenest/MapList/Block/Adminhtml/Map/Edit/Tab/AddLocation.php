<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/24/16
 * Time: 12:01
 */

namespace Magenest\MapList\Block\Adminhtml\Map\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Grid\Container;

class AddLocation extends Container implements TabInterface
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_map_edit_tab_addLocation';
        $this->_blockGroup = 'Magenest_MapList';

        parent::_construct();

        $this->removeButton('add');
    }

    public function getTabLabel()
    {
        return __('Add Location');
    }

    public function getTabTitle()
    {
        return __('Add Location');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
