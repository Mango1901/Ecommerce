<?php

namespace Magenest\CustomSource\Block\Element;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class SetAreaQuote
 */
class SetAreaQuote extends Template
{
    /**
     * @var Session
     */
    private $_checkoutSession;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        array   $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    /**
     * Get quote object associated with cart. By default it is current customer session quote
     *
     * @return mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteData()
    {
        $quote = $this->_checkoutSession->getQuote();
        if (!$this->hasData('quote') && $quote->getIsActive()) {
            $this->setData('quote', $quote);
        }
        return $this->_getData('quote');
    }
}
