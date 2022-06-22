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

namespace Magenest\StoreCredit\Plugin;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;

/**
 * Class DisableStoreCredit
 * @package Magenest\StoreCredit\Plugin
 */
class DisableStoreCredit
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @param Cart $cart
     */
    public function __construct(
        Cart $cart
    ) {
        $this->cart = $cart;
    }

    /**
     * @param Action $subject
     */
    public function beforeExecute(Action $subject)
    {
        $quote = $this->cart->getQuote();
        if ($quote->getMpStoreCreditSpent()) {
            $quote->setMpStoreCreditSpent(0);
            $quote->setMpStoreCreditBaseDiscount(0);
            $quote->setMpStoreCreditDiscount(0);
            $this->cart->saveQuote();
        }
    }
}
