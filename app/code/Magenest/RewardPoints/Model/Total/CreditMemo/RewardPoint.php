<?php

namespace Magenest\RewardPoints\Model\Total\CreditMemo;

use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class RewardPoint
 * @package Magenest\RewardPoints\Model\Total\CreditMemo
 */
class RewardPoint extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;


    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * RewardPoint constructor.
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_helper        = $helper;
        $this->cartRepository = $cartRepository;
        $this->priceCurrency  = $priceCurrencyInterface;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($data);
    }

    /**
     * @param Creditmemo $creditmemo
     *
     * @return Creditmemo\Total\AbstractTotal|void
     */
    public function collect(Creditmemo $creditmemo)
    {
        $orderModel  = $creditmemo->getOrder();
        $rewardPoint = 0;
        try {
            if ($this->_helper->getEnableModule()) {
                $rewardAllAmount       = $orderModel->getData('reward_amount');
                $orderSubtotal         = $orderModel->getBaseSubtotal();
                $orderSubtotalRefund   = $orderModel->getSubtotalRefunded();
                $currentRefundSubtotal = $creditmemo->getBaseSubtotal();
                $rewardAmountRefunded  = $this->getRewardRefunded($creditmemo->getOrderId());
                if (($currentRefundSubtotal + $orderSubtotalRefund) == $orderSubtotal) {
                    $rewardAmount = $rewardAllAmount - $rewardAmountRefunded;
                    if ($orderModel->getRewardPoint()) {
                        $rewardPoint = $orderModel->getRewardPoint() - $this->getRewardPointRefund($creditmemo->getOrderId());
                    }
                } else {
                    if ($orderModel->getRewardPoint()) {
                        $rewardPoint  = ceil($orderModel->getRewardPoint() * $currentRefundSubtotal / $orderSubtotal);
                        $rewardAmount = $this->_helper->getRewardBaseAmount($rewardPoint);
                    } else {
                        $rewardAmount = round($rewardAllAmount * $currentRefundSubtotal / $orderSubtotal, 2);
                    }
                }

                if ($rewardAmount) {
                    if ($orderModel->getRewardPoint()) {
                        $creditmemo->setRewardPoint($rewardPoint);
                    } elseif ($orderModel->getRewardTier())
                        $creditmemo->setRewardTier($orderModel->getRewardTier());
                    $creditmemo->setData('reward_amount', $rewardAmount);
                    $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $rewardAmount)
                        ->setGrandTotal($creditmemo->getGrandTotal() - $rewardAmount);
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param $orderId
     *
     * @return int
     */
    public function getRewardRefunded($orderId)
    {
        $rewardAmount        = 0;
        $credimemoCollection = $this->collectionFactory->create()->addFieldToFilter('order_id', $orderId);
        foreach ($credimemoCollection as $creditmemo) {
            if ($creditmemo->getRewardAmount()) {
                $rewardAmount += $creditmemo->getRewardAmount();
            }
        }

        return $rewardAmount;
    }

    /**
     * @param $orderId
     *
     * @return int
     */
    public function getRewardPointRefund($orderId)
    {
        $rewardPoint         = 0;
        $creditmemoCollection = $this->collectionFactory->create()->addFieldToFilter('order_id', $orderId);
        foreach ($creditmemoCollection as $creditmemo) {
            if ($creditmemo->getRewardPoint()) {
                $rewardPoint += $creditmemo->getRewardPoint();
            }
        }

        return $rewardPoint;
    }
}
