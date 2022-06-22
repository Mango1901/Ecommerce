<?php


namespace Magenest\Affiliate\Plugin\MultiShipping;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;

/**
 * Class AbstractMultiShipping
 * @package Magenest\Affiliate\Plugin\MultiShipping
 */
abstract class AbstractMultiShipping
{
    /**
     * @var Cart
     */
    protected $cart;

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
        if ($quote->getIsMultiShipping()) {
            $quote->setIsMultiShipping(0);
            $extensionAttributes = $quote->getExtensionAttributes();
            if ($extensionAttributes && $extensionAttributes->getShippingAssignments()) {
                $extensionAttributes->setShippingAssignments([]);
            }
            $this->cart->saveQuote();
        }
    }
}
