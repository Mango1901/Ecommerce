<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class QuoteUpdateItemBefore
 * @package Magenest\OrderExtraInformation\Observer
 */
class QuoteUpdateItemBefore implements ObserverInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Checkout\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        /** @var \Magento\Framework\DataObject $info */
        $info = $observer->getEvent()->getInfo();

        foreach ($info->getData() as $itemId => $itemInfo) {
            $item = $cart->getQuote()->getItemById($itemId);
            if (!$item || !isset($itemInfo['customer_note'])) {
                continue;
            }

            $item->setCustomerNote($itemInfo['customer_note']);
        }

        return $this;
    }
}
