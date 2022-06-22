<?php
namespace Magenest\Affiliate\Model;

use Magenest\Affiliate\Api\CouponManagementInterface;
use Magenest\Affiliate\Helper\Calculation\DiscountFactory;
use Magenest\Affiliate\Helper\Data;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

class CouponManagement implements CouponManagementInterface
{
    /**
     * Quote repository.
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /** @var Data */
    private $helperData;

    /** @var DiscountFactory */
    private $discountHelper;

    /**
     * CouponManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param DiscountFactory $discountHelper
     * @param Data $helperData
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        DiscountFactory         $discountHelper,
        Data                    $helperData
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helperData      = $helperData;
        $this->discountHelper  = $discountHelper;
    }

    /**
     * @inheritDoc
     */
    public function set($cartId, $affiliateCouponCode)
    {
        /** @var  Quote $quote */
        $quote   = $this->quoteRepository->getActive($cartId);
        $storeId = $quote->getStoreId();
        if (!$this->helperData->isEnabled($storeId) || !$this->helperData->isUseCodeAsCoupon($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        $couponWithPreFix = explode('-', $affiliateCouponCode);
        if (count($couponWithPreFix) !== 2) {
            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again."));
        }

        /** @var Campaign $campaign */
        $currentAffiliate = $this->helperData->getAffiliateAccount($quote->getCustomerId(), 'customer_id');
        $affiliateAccount = $this->helperData->getAffiliateAccount($couponWithPreFix[0], 'code');
        $discountHelper   = $this->helper($quote);
//        $campaigns        = count($discountHelper->getAvailableCampaign($affiliateAccount, $couponWithPreFix[1]));

        if (!$affiliateAccount->getId() || ($currentAffiliate && $currentAffiliate->getId())
            || $discountHelper->isAffiliateCatalogRuleApplied($quote)
            || $quote->getIsMultiShipping()) {
            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again."));
        }

        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        if (!$quote->getStoreId()) {
            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
        }

        $this->helperData->setAffiliateKeyToCookie($affiliateCouponCode);
        $this->helperData->setAffiliateKeyToCookie('coupon', Data::AFFILIATE_COOKIE_SOURCE_NAME);
        $quote->collectTotals()->save();

        return true;
    }

    /**
     * @param Quote $quote
     *
     * @return \Magenest\Affiliate\Helper\Calculation\Discount
     */
    private function helper(Quote $quote)
    {
        return $this->discountHelper->create(['address' => $quote->getShippingAddress()]);
    }

    /**
     * @inheritDoc
     */
    public function remove($cartId)
    {
        /** @var  Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $this->helperData->deleteAffiliateCookieSourceName();
        $this->helperData->resetAffiliate($quote);
        $quote->collectTotals()->save();

        return true;
    }
}
