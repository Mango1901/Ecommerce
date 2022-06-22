<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/24/16
 * Time: 12:01
 */

namespace Magenest\MapList\Block\Adminhtml\Holiday\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Grid\Container;

class AddStore extends Container implements TabInterface
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_holiday_edit_tab_addStore';
        $this->_blockGroup = 'Magenest_MapList';

        parent::_construct();

        $this->removeButton('add');
    }

    public function getTabLabel()
    {
        return __('Add Store');
    }

    public function getTabTitle()
    {
        return __('Add Store');
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
