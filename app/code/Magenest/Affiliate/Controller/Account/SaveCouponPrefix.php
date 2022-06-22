<?php


namespace Magenest\Affiliate\Controller\Account;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Account;

/**
 * Class SaveCouponPrefix
 * @package Magenest\Affiliate\Controller\Account
 */
class SaveCouponPrefix extends Account
{
    /**
     * @return Redirect|Page
     */
    public function execute()
    {
        $couponPrefix       = $this->getRequest()->getParam('campaign_coupon_prefix');
        $affiliateAccount   = $this->accountFactory->create()->loadByCode($couponPrefix);
        $affiliateAccountId = $affiliateAccount->getId();

        $currentAffiliate   = $this->accountFactory->create()->load(
            $this->customerSession->getCustomerId(),
            'customer_id'
        );
        $currentAffiliateId = $currentAffiliate->getId();

        if ($currentAffiliateId) {
            if ($affiliateAccountId === null || $affiliateAccountId === $currentAffiliateId) {
                try {
                    $currentAffiliate->setData('code', $couponPrefix)->save();
                    $this->messageManager->addSuccessMessage(__('Successfully'));
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            } else {
                $this->messageManager->addErrorMessage(__('Coupon prefix is exists.'));
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*');
    }
}
