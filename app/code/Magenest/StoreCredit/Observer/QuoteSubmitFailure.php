<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magenest\StoreCredit\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class QuoteSubmitFailure
 * @package Magenest\StoreCredit\Observer
 */
class QuoteSubmitFailure implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * QuoteSubmitFailure constructor.
     *
     * @param Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        $credit = $order->getMpStoreCreditBaseDiscount();

        if ($credit > 0.0001) {
            try {
                $this->helper->addTransaction(
                    Data::ACTION_REVERT,
                    $order->getCustomerId(),
                    $credit,
                    $order
                );
            } catch (LocalizedException $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $this;
    }
}
