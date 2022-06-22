<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class RemoveCookie
 * @package Magenest\RewardPoints\Observer
 */
class RemoveCookie implements ObserverInterface
{
    /**
     * @var \Magenest\RewardPoints\Cookie\ReferralCode
     */
    protected $referralCode;

    /**
     * Logout constructor.
     * @param \Magenest\RewardPoints\Cookie\ReferralCode $referralCode
     */
    public function __construct(
        \Magenest\RewardPoints\Cookie\ReferralCode $referralCode
    ) {
        $this->referralCode = $referralCode;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //Delete cookie customer when logout
        $this->referralCode->delete();
    }
}
