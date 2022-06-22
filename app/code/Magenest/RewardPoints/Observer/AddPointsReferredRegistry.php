<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddPointsReferredRegistry
 * @package Magenest\RewardPoints\Observer
 */
class AddPointsReferredRegistry implements ObserverInterface
{
    /**
     * @var \Magenest\RewardPoints\Model\ReferralPointsFactory
     */
    protected $referralPointsFactory;

    /**
     * AddPointsReferredRegistry constructor.
     * @param \Magenest\RewardPoints\Model\ReferralPointsFactory $referralPointsFactory
     */
    public function __construct(
        \Magenest\RewardPoints\Model\ReferralPointsFactory $referralPointsFactory
    ) {
        $this->referralPointsFactory = $referralPointsFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $observer->getEvent()->getRule();
        if (!in_array($rule->getCondition(), ['referafriend', 'earn_when_referee_clicked'])) {
            return;
        }
        $referralPoints = $this->referralPointsFactory->create()->load($rule->getId(), 'rule_id');

        // Add more data to current rule before adding to registry
        $rule->setData('points_referred', $referralPoints->getPointsReferred() + 0);
    }
}
