<?php


namespace Magenest\Affiliate\Block\Adminhtml\Group\Create;

/**
 * Class Tabs
 * @package Magenest\Affiliate\Block\Adminhtml\Group\Create
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('group_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Group Information'));
    }
}
