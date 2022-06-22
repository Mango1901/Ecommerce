<?php


namespace Magenest\Affiliate\Block\Adminhtml\Account\Edit;

/**
 * Class Tabs
 * @package Magenest\Affiliate\Block\Adminhtml\Account\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('account_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Account Information'));
    }
}
