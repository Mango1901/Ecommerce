<?php


namespace Magenest\Affiliate\Block\Adminhtml\Campaign\Edit;

/**
 * Class Tabs
 * @package Magenest\Affiliate\Block\Adminhtml\Campaign\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('campaign_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Campaign Information'));
    }
}
