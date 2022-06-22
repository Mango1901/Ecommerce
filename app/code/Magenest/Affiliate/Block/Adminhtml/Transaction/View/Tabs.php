<?php


namespace Magenest\Affiliate\Block\Adminhtml\Transaction\View;

/**
 * Class Tabs
 * @package Magenest\Affiliate\Block\Adminhtml\Transaction\View
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('transaction_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Transaction Information'));
    }
}
