<?php

namespace Magenest\RewardPoints\Controller\Quote;

use Magenest\RewardPoints\Model\AccountFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Process
 *
 * @package Magenest\RewardPoints\Controller\Quote
 */
class Process extends Add
{
    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $quote          = $this->checkoutSession->getQuote();
        $grandTotal     = $quote->getBaseGrandTotal();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ((boolean)$this->getRequest()->getParam('isCart')) {
            $this->removePointIntoQuote();
            $this->cart->getQuote()->collectTotals()->save();
            return $resultRedirect;
        }
        if ($this->_helper->getRewardTiersEnable()) {
            if ($grandTotal < 0) {
                $rewardAmount = $grandTotal + $quote->getRewardAmount();
                $tier         = $rewardAmount / $quote->getSubtotal() * 100;
                $quote->setData('reward_tier', $tier)->save();
                $quote->setData('reward_amount', $rewardAmount)->save();
                $quote->setTotalsCollectedFlag(false);
                $quote->collectTotals()->save();
            }
        } else {
            $point = $quote->getRewardPoint();
            if ($grandTotal < 0) {
                $newRewardAmount = $grandTotal + $quote->getRewardAmount();
                $point           = ceil($newRewardAmount * $this->_helper->getRewardBaseAmount());
            }

            $this->addPoint($point);
        }

        return $resultRedirect;
    }

    /**
     * @throws \Exception
     */
    private function removePointIntoQuote()
    {
        $quote = $this->checkoutSession->getQuote();
        $quote->setData('reward_point', 0)->save();
        $quote->setData('reward_amount', 0)->save();
    }
}
