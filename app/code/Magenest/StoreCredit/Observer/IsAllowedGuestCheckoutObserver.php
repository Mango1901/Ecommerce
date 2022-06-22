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
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magenest\StoreCredit\Model\Product\Type\StoreCredit;

/**
 * Class IsAllowedGuestCheckoutObserver
 * @package Magenest\StoreCredit\Observer
 */
class IsAllowedGuestCheckoutObserver implements ObserverInterface
{
    /**
     * Check is allowed guest checkout if quote contain store credit product(s)
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $result = $observer->getEvent()->getResult();

        /* @var $quote Quote */
        $quote = $observer->getEvent()->getQuote();

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            if (($product = $item->getProduct()) && $product->getTypeId() == StoreCredit::TYPE_STORE_CREDIT) {
                $result->setIsAllowed(false);
                break;
            }
        }

        return $this;
    }
}
