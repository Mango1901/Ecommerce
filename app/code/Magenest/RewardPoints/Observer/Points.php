<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class Points implements ObserverInterface
{
    /**
     * @var \Magenest\RewardPoints\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magenest\RewardPoints\Model\ReferralPointsFactory
     */
    protected $referralPointsFactory;

    /**
     * Points constructor.
     * @param \Magenest\RewardPoints\Model\RuleFactory $ruleFactory
     * @param \Magenest\RewardPoints\Model\ReferralPointsFactory $referralPointsFactory
     */
    public function __construct(
        \Magenest\RewardPoints\Model\RuleFactory $ruleFactory,
        \Magenest\RewardPoints\Model\ReferralPointsFactory $referralPointsFactory
    )
    {
        $this->ruleFactory = $ruleFactory;
        $this->referralPointsFactory = $referralPointsFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (\Magenest\RewardPoints\Helper\Data::isReferAFriendModuleEnabled()) {
            $referralData = $observer->getData('referral_data');

            if (!isset($referralData['condition'])) return;

            // delete referring point record when change from refer a friend to other behavior rule
            if (!in_array($referralData['condition'], ['referafriend', 'earn_when_referee_clicked'])) {
                $currentRule = $this->ruleFactory->create()->load($referralData['id']);
                $currentCondition = $currentRule->getCondition();
                if (!in_array($currentCondition, ['referafriend', 'earn_when_referee_clicked'])) return;
                $this->referralPointsFactory->create()->load($referralData['id'], 'rule_id')->delete();
                return;
            }

            // saving refer a friend rule, if exist, update, if not, create
            $referralPoints = $this->referralPointsFactory->create()->load($referralData['id'], 'rule_id');
            $referralPoints->setData('rule_id', $referralData['id']);
            $referralPoints->setData('points_referring', $referralData['points'] ?? 0);
            $referralPoints->setData('points_referred', $referralData['points_referred'] ?? 0);
            $referralPoints->save();
        } else {
            $ruleBeforeSave = $observer->getEvent()->getCurrentRule();
            if ($ruleBeforeSave->getCondition() == 'referafriend' && $ruleBeforeSave->getStatus()) $ruleBeforeSave->setStatus(0);
        }

        return;
    }
}
