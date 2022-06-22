<?php


namespace Magenest\Affiliate\Model;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magenest\Affiliate\Api\GuestCouponManagementInterface;
use Magenest\Affiliate\Api\CouponManagementInterface;

class GuestCouponManagement implements GuestCouponManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CouponManagementInterface
     */
    private $couponManagement;

    /**
     * GuestCouponManagement constructor.
     *
     * @param CouponManagementInterface $couponManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        CouponManagementInterface $couponManagement,
        QuoteIdMaskFactory        $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->couponManagement   = $couponManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $affiliateCouponCode)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->couponManagement->set($quoteIdMask->getQuoteId(), $affiliateCouponCode);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->couponManagement->remove($quoteIdMask->getQuoteId());
    }
}
