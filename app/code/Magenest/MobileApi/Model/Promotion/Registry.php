<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Promotion;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magenest\MobileApi\Model\Helper;

/**
 * Class Registry
 * @package Magenest\MobileApi\Model\Promotion
 */
class Registry
{
    /**
     * @var bool
     */
    protected $_isPendingAllGift = false;

    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param Helper $helper
     */
    function __construct(
        CheckoutSession $checkoutSession,
        Helper $helper
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->_helper = $helper;
    }

    /**
     * Set is pending all gift
     *
     * @param bool $isPending
     * @return $this
     */
    public function setIsPendingAllGift($isPending = false)
    {
        $this->_isPendingAllGift = $isPending;

        return $this;
    }

    /**
     * Get is pending all gift
     *
     * @return bool
     */
    public function getIsPendingAllGift()
    {
        return $this->_isPendingAllGift;
    }

    /**
     * Clear quote
     *
     * @param $cartId
     * @return \Magento\Quote\Model\Quote
     */
    public function clearQuote($cartId)
    {
        $this->setIsPendingAllGift(true);
        $this->_helper->initialSession();
        $this->_checkoutSession->clearQuote()->setQuoteId($cartId);

        $quote = $this->_checkoutSession->getQuote();

        return $quote;
    }
}