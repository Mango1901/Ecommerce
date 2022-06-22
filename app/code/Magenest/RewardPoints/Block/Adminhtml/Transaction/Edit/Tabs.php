<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Transaction\Edit;

/**
 * Class Tabs
 * @package Magenest\RewardPoints\Block\Adminhtml\Transaction\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardpoints_transaction_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Magenest Reward Points Configuration'));
    }
}
