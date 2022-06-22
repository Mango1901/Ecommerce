<?php


namespace Magenest\Affiliate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magenest\Affiliate\Helper\Data;

/**
 * Class CustomerLogout
 * @package Magenest\Affiliate\Observer
 */
class CustomerLogout implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * CustomerLogout constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     * @throws FailureToSendException
     * @throws InputException
     */
    public function execute(Observer $observer)
    {
        $affCode = $this->_helper->getAffiliateKeyFromCookie();
        $affSource = $this->_helper->getAffiliateKeyFromCookie(Data::AFFILIATE_COOKIE_SOURCE_NAME);
        if ($affCode && $affSource === 'coupon') {
            $this->_helper->deleteAffiliateCookieSourceName();
        }

        return $this;
    }
}
