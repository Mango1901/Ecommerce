<?php
/**
 * Created by PhpStorm.
 * User: thang
 * Date: 24/08/2018
 * Time: 14:57
 */

namespace Magenest\RewardPoints\Block\Adminhtml\Order;

/**
 * Class Totals
 * @package Magenest\RewardPoints\Block\Adminhtml\Order
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
    /**
     * @return $this|\Magento\Sales\Block\Adminhtml\Order\Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $label = 'Reward Amount';
        if ($this->getSource()->getRewardPoint()) {
            $label = 'Reward Amount (' . (int)$this->getSource()->getRewardPoint() . ' points)';
        } else if ($this->getSource()->getRewardTier()) {
            $label = 'Reward Amount (' . (int)$this->getSource()->getRewardTier() . ' %)';
        };
        if ($this->getSource()->getRewardAmount() > 0 ) {
            $total = new \Magento\Framework\DataObject([
                'code'  => $this->getNameInLayout(),
                'label' => __($label),
                'value' => -$this->getSource()->getRewardAmount(),
            ]);
            $this->addTotal($total, 'subtotal');
        }

        return $this;
    }
}
