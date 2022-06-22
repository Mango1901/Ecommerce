<?php

namespace Magenest\Affiliate\Observer;

use Magenest\Affiliate\Helper\Data as AffiliateHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

/**
 * Class OrderPlaceAfter
 * @package Magenest\Affiliate\Observer
 */
class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var AffiliateHelper
     */
    protected $_helper;

    /**
     * OrderPlaceAfter constructor.
     * @param AffiliateHelper $helper
     */
    public function __construct(
        AffiliateHelper $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return mixed
     * @throws InputException
     * @throws FailureToSendException
     */
    public function execute(Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }
        $order = $observer->getEvent()->getOrder();
        if ($order->getAffiliateKey()) {
            $this->_helper->deleteAffiliateCookieSourceName();
        }
        return $this;
    }
}
