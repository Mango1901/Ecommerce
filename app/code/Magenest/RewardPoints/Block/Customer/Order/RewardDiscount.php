<?php

namespace Magenest\RewardPoints\Block\Customer\Order;


/**
 * Class RewardDiscount
 * @package Magenest\RewardPoints\Block\Customer\Order
 */
class RewardDiscount extends \Magento\Sales\Block\Adminhtml\Totals
{
    /**
     * RewardDiscount constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Initialize totals object.
     * @return $this
     */
    public function initTotals()
    {
        $label = 'Reward Amount';
        if ($this->getSource()->getRewardPoint()) {
            $label = 'Reward Amount (' . (int)$this->getSource()->getRewardPoint() . ' points)';
        } else if ($this->getSource()->getRewardTier()) {
            $label = 'Reward Amount (' . (int)$this->getSource()->getRewardTier() . ' %)';
        };
        if ($this->getSource()->getRewardAmount() > 0) {
            $total = new \Magento\Framework\DataObject([
                'code'  => $this->getNameInLayout(),
                'label' => __($label),
                'value' => -$this->getSource()->getRewardAmount(),
            ]);

            $this->getParentBlock()->addTotal($total, 'subtotal');
        }

        return $this;
    }
}
