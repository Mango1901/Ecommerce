<?php
namespace Magenest\RewardPoints\Block\Adminhtml\Edit\Tab;

/**
 * Class Transaction
 * @package Magenest\RewardPoints\Block\Adminhtml\Edit\Tab
 */
class Transaction extends \Magenest\RewardPoints\Block\Adminhtml\Grid
{

    /**
     * @return $this|\Magenest\RewardPoints\Block\Adminhtml\Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('rewardpoints/index/transactionHistory', ['_current' => true]);
    }
}
