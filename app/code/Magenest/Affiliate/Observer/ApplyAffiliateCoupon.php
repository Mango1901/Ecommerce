<?php
namespace Magenest\Affiliate\Observer;

use Magenest\Affiliate\Helper\Calculation\Discount;
use Magenest\Affiliate\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;

class ApplyAffiliateCoupon implements ObserverInterface
{
    /** @var Discount */
    private $discountHelper;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param Discount $discountHelper
     * @param LoggerInterface $logger
     */
    public function __construct(Discount $discountHelper, LoggerInterface $logger)
    {
        $this->logger         = $logger;
        $this->discountHelper = $discountHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote           = $observer->getEvent()->getQuote();
        $affiliateSource = $this->discountHelper->getAffiliateSourceFromCookie(Data::AFFILIATE_COOKIE_SOURCE_NAME);
        try {
            if (!(bool)strlen($quote->getCouponCode())
                && $this->discountHelper->canCalculate($quote->getStoreId(), true)
                && !$quote->getIsMultiShipping()
                && !$this->discountHelper->isAffiliateCatalogRuleApplied($quote)
                && $affiliateSource === 'coupon') {

                $affiliateKey = $this->discountHelper->getAffiliateKeyFromCookie();
                $quote->setCouponCode($affiliateKey);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
