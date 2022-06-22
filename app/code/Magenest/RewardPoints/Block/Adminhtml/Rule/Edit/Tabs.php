<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Rule\Edit;

/**
 * Class Tabs
 * @package Magenest\RewardPoints\Block\Adminhtml\Rule\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardpoints_rule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Magenest Reward Points Configuration'));
    }
}
