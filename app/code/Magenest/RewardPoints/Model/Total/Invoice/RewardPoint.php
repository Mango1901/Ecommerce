<?php

namespace Magenest\RewardPoints\Model\Total\Invoice;


/**
 * Class RewardPoint
 * @package Magenest\RewardPoints\Model\Total\Invoice
 */
class RewardPoint extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $_helper;
//
//    /**
//     * @var \Magento\Quote\Api\CartRepositoryInterface
//     */
//    protected $cartRepository;

//    /**
//     * @var Calculator
//     */
//    protected $calculator;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * RewardPoint constructor.
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->priceCurrency = $priceCurrencyInterface;
        $this->quoteRepository = $quoteRepository;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($data);
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal|void
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {

        $orderModel = $invoice->getOrder();
        $quote = $this->quoteRepository->get($orderModel->getQuoteId());
        try {
            $rewardPoint = 0;
            if ($this->_helper->getEnableModule()) {
                $rewardAllAmount        = $quote->getRewardAmount();
                $orderSubtotal          = $orderModel->getBaseSubtotal();
                $orderSubtotalInvoiced  = $orderModel->getSubtotalInvoiced();
                $currentInvoiceSubtotal = $invoice->getBaseSubtotal();
                $rewardAmountInvoiced   = $this->getRewardInvoiced($invoice->getOrderId());
                if (($currentInvoiceSubtotal + $orderSubtotalInvoiced) == $orderSubtotal) {
                    $rewardAmount = $rewardAllAmount - $rewardAmountInvoiced;
                    if ($quote->getRewardPoint()) {
                        $rewardPoint = $quote->getRewardPoint() - $this->getRewardPointInvoiced($invoice->getOrderId());
                    }
                } else {
                    if ($quote->getRewardPoint()) {
//                        if ($orderModel->getShippingAmount() > 0) {
                        $allRewardPoint     = $quote->getRewardPoint();
                        /**
                         *
                         */
                        $rewardPoint        = $allRewardPoint *($invoice->getGrandTotal()) / ($orderModel->getGrandTotal()+ $quote->getRewardAmount());
                        $rewardPoint = ceil($rewardPoint);   // lam cho tron thoi
//                        } else
//                            $rewardPoint = ceil($orderModel->getRewardPoint() * $currentInvoiceSubtotal / $orderSubtotal);

                        $rewardAmount = $this->_helper->getRewardBaseAmount($rewardPoint);
                    } else {
                        $rewardAmount = round($rewardAllAmount * $currentInvoiceSubtotal / $orderSubtotal, 2);
                    }
                }
                if ($rewardAmount) {
                    if ($quote->getRewardPoint()) {
                        $invoice->setRewardPoint($rewardPoint);
                    } elseif ($quote->getRewardTier())
                        $invoice->setRewardTier($quote->getRewardTier());
                    $invoice->setData('reward_amount', $rewardAmount);
                    $baseRewardAmount = $this->_helper->convert($rewardAmount);
                    $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseRewardAmount)
                        ->setGrandTotal($invoice->getGrandTotal() - $baseRewardAmount);
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
    public function getRewardInvoiced($orderId)
    {
        $rewardAmount      = 0;
        $invoiceCollection = $this->collectionFactory->create()->addFieldToFilter('order_id', $orderId);
        foreach ($invoiceCollection as $invoice) {
            if ($invoice->getRewardAmount()) {
                $rewardAmount += $invoice->getRewardAmount();
            }
        }

        return $rewardAmount;
    }

    /**
     * @param $orderId
     *
     * @return int
     */
    public function getRewardPointInvoiced($orderId)
    {
        $rewardAmount      = 0;
        $invoiceCollection = $this->collectionFactory->create()->addFieldToFilter('order_id', $orderId);
        foreach ($invoiceCollection as $invoice) {
            if ($invoice->getRewardPoint()) {
                $rewardAmount += $invoice->getRewardPoint();
            }
        }

        return $rewardAmount;
    }
}
