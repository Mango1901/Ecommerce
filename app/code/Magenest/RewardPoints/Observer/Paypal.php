<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Paypal
 * @package Magenest\RewardPoints\Observer
 */
class Paypal implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * Paypal constructor.
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magenest\RewardPoints\Helper\Data $helper
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magenest\RewardPoints\Helper\Data $helper
    )
    {
        $this->quoteFactory = $quoteFactory;
        $this->cartRepository = $cartRepository;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Paypal\Model\Cart $cart */
        $cart = $observer->getEvent()->getData('cart');

        if ($cart) {
            $salesModel = $cart->getSalesModel();
            if ($quoteId = $salesModel->getDataUsingMethod('quote_id')) {
                $salesModel = $this->quoteFactory->create();
                try {
                    $salesModel = $this->cartRepository->get($quoteId);
                }catch (\Exception $e) {
                }
            }
            $value = $salesModel->getDataUsingMethod('reward_amount');
            if ($value > 0) {
                $baseValue = $this->helper->convert($value);
                $cart->addDiscount(floatval($baseValue));
            }
        }

    }
}