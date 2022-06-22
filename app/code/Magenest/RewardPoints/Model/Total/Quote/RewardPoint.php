<?php

namespace Magenest\RewardPoints\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Store\Model\Store;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

/**
 * Class RewardPoint
 * @package Magenest\RewardPoints\Model\Total\Quote
 */
class RewardPoint extends AbstractTotal
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_quoteSession;

    /**
     * @var
     */
    protected $_giftCard;

    /**
     * @var
     */
    protected $_usedGiftCard;

//    /**
//     * @var Calculator
//     */
//    protected $_calculator;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|null
     */
    protected $eventManager = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $_helper;

    /**
     * RewardPoint constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magenest\RewardPoints\Helper\Data $giftcardData
     * @param \Magenest\RewardPoints\Model\Total\Quote\Calculator $caculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\RewardPoints\Helper\Data $giftcardData,
//        \Magenest\RewardPoints\Model\Total\Quote\Calculator $caculator,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->_customerSession = $customerSession;
        $this->_quoteSession    = $quoteSession;
        $this->_helper          = $helper;
        $this->eventManager     = $eventManager;
        $this->storeManager     = $storeManager;
        $this->priceCurrency    = $priceCurrency;
        $this->_helper          = $giftcardData;
//        $this->_calculator      = $caculator;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return $this|AbstractTotal
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {

        AbstractTotal::collect($quote, $shippingAssignment, $total);

        $subtotal   = $total->getSubtotal();
        $customerId = $this->_quoteSession->getCustomerId();
        if (!$customerId) {
            $customerId = $this->_customerSession->getId();
        }

        try {
            if ($this->_helper->getEnableModule() && !empty($customerId) && $subtotal != 0) {
                $rewardAmount = $quote->getRewardAmount();
                $rewardPoint  = $quote->getRewardPoint();
                $total->addTotalAmount($this->getCode(), -$rewardAmount);
                $total->addBaseTotalAmount($this->getCode(), -$rewardAmount);
                $quote->setRewardAmount($rewardAmount);
                if (intval($quote->getBaseSubtotal()) && intval($rewardAmount))
                    $quote->setBaseRewardAmount($this->_helper->convert($rewardAmount));
                $total->setRewardAmount($rewardAmount);
                $total->setRewardPoint($rewardPoint);
            }
        } catch
        (\Exception $e) {
        }

        return $this;
    }


    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        if ($this->_helper->getEnableModule()) {
            $allShippingAddresses = $quote->getAllShippingAddresses();
            $amount = count($allShippingAddresses) ? floatval($quote->getData('reward_amount')) / count($allShippingAddresses) : 0;
            $pointSpent = count($allShippingAddresses) ? floatval($quote->getData('reward_point')) / count($allShippingAddresses) : 0;
            $tierDiscount = count($allShippingAddresses) ? floatval($quote->getData('reward_tier')) / count($allShippingAddresses) : 0;
            $rewardLabel = $this->_helper->getPointUnit();
            if ($this->_helper->getRewardTiersEnable()) {
                $title = __('Reward Amount (' . $tierDiscount . ' %)');
            } else {
                $title = __('Reward Amount (' . $pointSpent . ' ' . $rewardLabel . ')');
            }
            if ($amount != 0) {
                $result = [
                    'code' => $this->getCode(),
                    'title' => $title,
                    'value' => -$amount,
                ];
            }

        }
        return $result;
    }
}
