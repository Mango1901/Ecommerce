<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_RewardPoints
 */

namespace Magenest\RewardPoints\Block\Adminhtml\Order\Creditmemo;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Refund to reward point functionality block.
 *
 */
class Controls extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $_hlp;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param \Magenest\RewardPoints\Helper\Data $hlp
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magenest\RewardPoints\Helper\Data $hlp,
        array $data = []
    ) {
        $this->_hlp = $hlp;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Check whether refund to customer balance is available
     *
     * @return bool
     */
    public function canRefundToMgnRewardPoints()
    {
        if ($this->getCreditMemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }

    /**
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    protected function getCreditMemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    public function orderUseRewardPoints()
    {
        $rewardAmount = $this->getCreditMemo()->getOrder()->getRewardAmount();
        if (isset($rewardAmount)) {
            return $rewardAmount && (float)$rewardAmount > 0 ? true : false;
        } else {
            return null;
        }
    }

    /**
     * Populate amount to be refunded to reward point
     *
     * @return float
     */
    public function getReturnValue()
    {
        $customerBalance = $this->getCreditMemo()->getBaseGrandTotal();

        return $customerBalance ?: 0;
    }

    /**
     * Populate amount to be refunded to reward point
     *
     * @return float
     */
    public function getReturnPointValue()
    {
        $returnValue = $this->getReturnValue();
        $pointValue = $this->getPointValue();
        $pointAmount = $pointValue * $returnValue;
        $upOrDown = $this->_hlp->getUpOrDown();
        if ($upOrDown === 'up') {
            return ceil($pointAmount);
        } else {
            return floor($pointAmount);
        }
    }

    /**
     * Return reward point amount that customer can gain back when order is refunded
     *
     * @return mixed
     */
    public function getReturnAppliedPoints()
    {
        return $this->getCreditMemo()->getRewardPoint();
    }

    /**
     * Using if customer want to change label point displayed, default: points
     *
     * @return mixed
     */
    public function getPointLabel()
    {
        return $this->_hlp->getPointUnit();
    }

    public function getCurrentCurrencySymbol()
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }

    public function getPointValue()
    {
        $pointValue = $this->_scopeConfig->getValue('rewardpoints/point_config/points_money',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return (float)$pointValue;
    }

    public function getUpOrDown()
    {
        return $this->_hlp->getUpOrDown();
    }
}
