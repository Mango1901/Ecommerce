<?php


namespace Magenest\Affiliate\Block\Account;

use Magenest\Affiliate\Block\Account;
use Magenest\Affiliate\Helper\Data as AffiliateHelper;
use Magenest\Affiliate\Helper\Payment;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magenest\RewardPoints\Api\GetReferralCodeByCustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Refer
 * @package Magenest\Affiliate\Block\Account
 */
class Refer extends Account
{
    /**
     * @var GetReferralCodeByCustomerInterface
     */
    protected $getReferralCodeByCustomer;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param View $helperView
     * @param AffiliateHelper $affiliateHelper
     * @param Payment $paymentHelper
     * @param JsonHelper $jsonHelper
     * @param Registry $registry
     * @param PriceHelper $pricingHelper
     * @param ObjectManagerInterface $objectManager
     * @param CampaignFactory $campaignFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param TransactionFactory $transactionFactory
     * @param GetReferralCodeByCustomerInterface $getReferralCodeByCustomer
     * @param array $data
     */
    public function __construct(
        Context                            $context,
        Session                            $customerSession,
        View                               $helperView,
        AffiliateHelper                    $affiliateHelper,
        Payment                            $paymentHelper,
        JsonHelper                         $jsonHelper,
        Registry                           $registry,
        PriceHelper                        $pricingHelper,
        ObjectManagerInterface             $objectManager,
        CampaignFactory                    $campaignFactory,
        AccountFactory                     $accountFactory,
        WithdrawFactory                    $withdrawFactory,
        TransactionFactory                 $transactionFactory,
        GetReferralCodeByCustomerInterface $getReferralCodeByCustomer,
        array                              $data = []
    ) {
        $this->getReferralCodeByCustomer = $getReferralCodeByCustomer;
        parent::__construct(
            $context,
            $customerSession,
            $helperView,
            $affiliateHelper,
            $paymentHelper,
            $jsonHelper,
            $registry,
            $pricingHelper,
            $objectManager,
            $campaignFactory,
            $accountFactory,
            $withdrawFactory,
            $transactionFactory,
            $data
        );
    }

    /**
     * @return string
     */
    public function getSendMailUrl()
    {
        return $this->getUrl('*/*/referemail');
    }

    /**
     * @return string
     */
    public function getSharingParam()
    {
        return $this->_affiliateHelper->getSharingParam();
    }

    /**
     * @return string
     */
    public function getSharingEmail()
    {
        return $this->getCustomer()->getEmail();
    }

    /**
     * @return mixed
     */
    public function getSharingCode()
    {
        return $this->getCurrentAccount()->getCode() ??
            $this->getReferralCodeByCustomer->execute($this->getCustomer()->getId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSocialContent()
    {
        $content = $this->_affiliateHelper->getDefaultMessageShareViaSocial();
        $storeModel = $this->_storeManager->getStore();

        return str_replace([
            '{{store_name}}',
            '{{refer_url}}'
        ], [
            $storeModel->getFrontendName(),
            $this->getSharingUrl()
        ], $content);
    }

    /**
     * @return string
     */
    public function getSharingUrl()
    {
        return $this->_affiliateHelper->getSharingUrl();
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getEmailContent()
    {
        $content = $this->_affiliateHelper->getDefaultEmailBody();
        $storeModel = $this->_storeManager->getStore();

        return str_replace([
            '{{store_name}}',
            '{{refer_url}}',
            '{{account_name}}'
        ], [
            $storeModel->getFrontendName(),
            $this->getSharingUrl(),
            $this->getCustomer()->getName()
        ], $content);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Refer friends'));

        return parent::_prepareLayout();
    }
}
