<?php


namespace Magenest\Affiliate\Helper\Calculation;

use Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Item;
use Magenest\Affiliate\Helper\Calculation;

class Discount extends Calculation
{
    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     *
     * @return $this|Calculation
     * @throws LocalizedException
     */
    public function collect(
        Quote                       $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total                       $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $items = $shippingAssignment->getItems();

        if (!$items || !$this->canCalculate($quote->getStoreId(), true)
            || $quote->getIsMultiShipping()
            || $this->isAffiliateCatalogRuleApplied($quote)) {
            $this->resetAffiliate($quote);
            return $this;
        }

        $itemFields = ['affiliate_discount_amount', 'base_affiliate_discount_amount', 'base_discount_customer_affiliate', 'discount_customer_affiliate'];
        $this->resetAffiliateData(
            $items,
            $quote,
            array_merge($itemFields, ['affiliate_discount_shipping_amount', 'affiliate_base_discount_shipping_amount']),
            $itemFields
        );
        $account            = $this->registry->registry('mp_affiliate_account');
        $isBreakCampaign    = false;
        $affiliateKey       = $this->getAffiliateKey(); // if no cookie then first order key
        $affSource          = $this->getAffiliateSourceFromCookie(self::AFFILIATE_COOKIE_SOURCE_NAME);
        $campaignCouponCode = null;

        if ($affSource === 'coupon') {
            $affCodeWithPrefix = explode('-', $affiliateKey);
            if (count($affCodeWithPrefix) === 2) {
                $campaignCouponCode = $affCodeWithPrefix[1];
            }
        }

        if (!$this->getAvailableCampaign($account, $campaignCouponCode)) {
            $this->resetAffiliate($quote);
            return $this;
        }

        foreach ($this->getAvailableCampaign($account, $campaignCouponCode) as $campaign) {
            $isCalculateTax      = $campaign->getApplyDiscountOnTax();
            $isCalculateShipping = $campaign->getApplyToShipping();
            $totalCart           = $this->getTotalOnCart($items, $quote, $isCalculateShipping, $isCalculateTax, false);

            if ($totalCart <= 0.001) {
                break;
            }

            $totalDiscount = $this->getDiscountOnCampaign($campaign, $totalCart);
            if ($quote->getBaseAffiliateDiscountAmount() + $totalDiscount > $totalCart) {
                $totalDiscount   = $totalCart - $quote->getBaseAffiliateDiscountAmount();
                $isBreakCampaign = true;
            }
            $baseDiscount = $discount = $baseDiscountCustomerAffiliate = $discountCustomerAffiliate = 0;

//            if ($totalDiscount) {
                $lastItem = null;
                foreach ($items as $item) {
                    if ($item->getParentItem()) {
                        continue;
                    }

                    if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                        /** @var Item $child */
                        foreach ($item->getChildren() as $child) {
                            $this->calculateItemDiscount(
                                $child,
                                $totalCart,
                                $totalDiscount,
                                $baseDiscount,
                                $discount,
                                $isCalculateTax,
                                $lastItem,
                                $account->getId(),
                                $campaign->getId(),
                                $baseDiscountCustomerAffiliate,
                                $discountCustomerAffiliate
                            );
                        }
                    } else {
                        $this->calculateItemDiscount(
                            $item,
                            $totalCart,
                            $totalDiscount,
                            $baseDiscount,
                            $discount,
                            $isCalculateTax,
                            $lastItem,
                            $account->getId(),
                            $campaign->getId(),
                            $baseDiscountCustomerAffiliate,
                            $discountCustomerAffiliate
                        );
                    }
                }

                if ($campaign->getApplyToShipping()) {
                    $shippingBaseDiscount = $totalDiscount - $baseDiscount;
                    $shippingDiscount     = $this->priceCurrency->convert($shippingBaseDiscount);
                    $quote->setAffiliateDiscountShippingAmount(
                        $quote->getAffiliateDiscountShippingAmount() + $shippingDiscount
                    );
                    $quote->setBaseAffiliateDiscountShippingAmount(
                        $shippingBaseDiscount
                    );
                    $baseDiscount += $shippingBaseDiscount;
                    $discount     += $shippingDiscount;
                } elseif ($lastItem && $totalDiscount > $baseDiscount) {
                    $lastItemBaseDiscount = $totalDiscount - $baseDiscount;
                    $lastItemDiscount     = $this->priceCurrency->convert($lastItemBaseDiscount);
                    $lastItem->setBaseAffiliateDiscountAmount(
                        $item->getBaseAffiliateDiscountAmount() + $lastItemBaseDiscount
                    )->setAffiliateDiscountAmount($item->getAffiliateDiscountAmount() + $lastItemDiscount);
                    $baseDiscount += $lastItemBaseDiscount;
                    $discount     += $lastItemDiscount;
                }

                $quote->setDiscountCustomerAffiliate($quote->getDiscountCustomerAffiliate() + $discountCustomerAffiliate);
                $quote->setBaseDiscountCustomerAffiliate($quote->getBaseDiscountCustomerAffiliate() + $baseDiscountCustomerAffiliate);

                $quote->setAffiliateDiscountAmount($quote->getAffiliateDiscountAmount() + $discount);
                $quote->setBaseAffiliateDiscountAmount($quote->getBaseAffiliateDiscountAmount() + $baseDiscount);
                $baseGrandTotal = $total->getBaseGrandTotal() - $baseDiscount;
                $grandTotal     = $total->getGrandTotal() - $discount;
                if ($grandTotal <= 0.0001) {
                    $baseGrandTotal = $grandTotal = 0;
                }

                $quote->setAffiliateKey($this->getAffiliateKeyFromCookie() ?? $account->getAccountId());
                $total->setBaseGrandTotal($baseGrandTotal);
                $total->setGrandTotal($grandTotal);
                $quote->setBaseGrandTotal($baseGrandTotal);
                $quote->setGrandTotal($grandTotal);
                $quote->save();

                if ($isBreakCampaign) {
                    break;
                }
//            }
        }

        return $this;
    }

    /**
     * @param $item
     * @param $total
     * @param $totalDiscount
     * @param $baseDiscount
     * @param $discount
     * @param $isCalculateTax
     * @param $lastItem
     * @param $affiliateAccountId
     * @param $campaignId
     * @param $discountCustomerAffiliate
     */
    public function calculateItemDiscount(
        $item,
        $total,
        $totalDiscount,
        &$baseDiscount,
        &$discount,
        $isCalculateTax,
        &$lastItem,
        $affiliateAccountId,
        $campaignId,
        &$baseDiscountCustomerAffiliate,
        &$discountCustomerAffiliate
    ) {
        $itemTotalForDiscount = $this->getItemTotalForDiscount($item, $isCalculateTax, false);
        $itemBaseDiscount = ($itemTotalForDiscount / $total) * $totalDiscount;
        $itemBaseDiscount = $this->round($itemBaseDiscount, 'base');
        $itemDiscount     = $this->convertPrice($itemBaseDiscount, false, false, $item->getStoreId());
        $itemDiscount     = $this->round($itemDiscount);

        $itemAffiliateDiscount = $this->calculateItemAffiliateDiscount(
            $itemTotalForDiscount,
            $itemBaseDiscount,
            $item->getStoreId(),
            $affiliateAccountId,
            $campaignId
        );

        $affiliateDiscountBase = $itemAffiliateDiscount['affiliate_discount_base'] ?? 0;
        $affiliateDiscount = $itemAffiliateDiscount['affiliate_discount'] ?? 0;

        $item->setDiscountCustomerAffiliate($item->getDiscountCustomerAffiliate() + $affiliateDiscount)
            ->setBaseDiscountCustomerAffiliate($item->getBaseDiscountCustomerAffiliate() + $affiliateDiscountBase)
            ->setBaseAffiliateDiscountAmount($item->getBaseAffiliateDiscountAmount() + $itemBaseDiscount + $affiliateDiscountBase)
            ->setAffiliateDiscountAmount($item->getAffiliateDiscountAmount() + $itemDiscount + $affiliateDiscount);
        $baseDiscount += ($itemBaseDiscount + $affiliateDiscountBase);
        $discount     += ($itemDiscount + $affiliateDiscount);
        $baseDiscountCustomerAffiliate += $affiliateDiscountBase;
        $discountCustomerAffiliate += $affiliateDiscount;
        $lastItem     = $item;
    }

    private function calculateItemAffiliateDiscount(
        $itemTotalForDiscount,
        $itemBaseDiscount,
        $storeId,
        $affiliateAccountId,
        $campaignId
    ) {
        $result = [];

        $fieldSuffix   = $this->hasFirstOrder() ? '_second' : '';

        $commissionDiscount = $this->getCommissionDiscount->execute($affiliateAccountId, $campaignId);
        // Case discount affiliate of customer is fix then plus to discound of customer
        if ($commissionDiscount['type' . $fieldSuffix] == Arraycommission::TYPE_FIXED) {
            $result['affiliate_discount_base'] = $this->round($commissionDiscount['customer_value' . $fieldSuffix], 'base');
        } else {
            // Case discount affiliate of customer is percent then calculate based remain total after discount
            $remainAfterDiscount = $itemTotalForDiscount - $itemBaseDiscount;
            $affiliateDiscountBase = ($commissionDiscount['customer_value' . $fieldSuffix] / 100) * $remainAfterDiscount;
            $result['affiliate_discount_base'] = $this->round($affiliateDiscountBase, 'base');
        }
        $result['affiliate_discount'] = $this->convertPrice($result['affiliate_discount_base'], false, false, $storeId);
        return $result;
    }
}
