<?php


namespace Magenest\Affiliate\Block\Adminhtml\Withdraw\Edit;

/**
 * Class Tabs
 * @package Magenest\Affiliate\Block\Adminhtml\Withdraw\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('withdraw_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Withdraw Information'));
    }
}
