<?php


namespace Magenest\Affiliate\Block;

use Magenest\Affiliate\Helper\Data as AffiliateHelper;
use Magenest\Affiliate\Helper\Payment;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\Campaign\Display;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\ResourceModel\CommissionDiscount\CollectionFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Dashboard
 * @package Magenest\Affiliate\Block
 */
class Dashboard extends Account
{
    const COMMISSION_DISCOUNT_KEY = [
        'customer_value',
        'customer_value_second'
    ];
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

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
     * @param PriceCurrencyInterface $priceCurrency
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context                $context,
        Session                $customerSession,
        View                   $helperView,
        AffiliateHelper        $affiliateHelper,
        Payment                $paymentHelper,
        JsonHelper             $jsonHelper,
        Registry               $registry,
        PriceHelper            $pricingHelper,
        ObjectManagerInterface $objectManager,
        CampaignFactory        $campaignFactory,
        AccountFactory         $accountFactory,
        WithdrawFactory        $withdrawFactory,
        TransactionFactory     $transactionFactory,
        PriceCurrencyInterface $priceCurrency,
        CollectionFactory      $collectionFactory,
        array                  $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->collectionFactory = $collectionFactory;
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
     * @param number $rowSpan
     * @param mixed $campaign
     *
     * @return mixed
     */
    public function getCampaignRowSpan($rowSpan, $campaign)
    {
        $container = new DataObject(
            [
                'row_span' => ($this->isActiveAffiliateAccount() && $campaign->getCouponCode())
                    ? ($rowSpan + 4) : ($rowSpan + 3),
                'campaign' => $campaign
            ]
        );
        $this->_eventManager->dispatch('magenest_affiliate_dashboard_campaign_row_span', [
            'container' => $container
        ]);

        return $container->getRowSpan();
    }

    /**
     * @return bool
     */
    public function isActiveAffiliateAccount()
    {
        $affAccount = $this->accountFactory->create()->load($this->customerSession->getCustomerId(), 'customer_id');

        return (int)$affAccount->getStatus() === Status::ACTIVE;
    }

    /**
     * @param string $name
     * @param mixed $campaign
     *
     * @return mixed|string
     */
    public function getCommissionCampaignAddition($name, $campaign)
    {
        $child = $this->getChild($name);
        if ($child) {
            $child->setCampaign($campaign);
            if (!$this->hasData('commission_campaign_addition_' . $campaign->getId())) {
                $this->setData('commission_campaign_addition_' . $campaign->getId(), $child->toHtml());

                return $this->getData('commission_campaign_addition_' . $campaign->getId());
            }
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getCouponPrefix()
    {
        return $this->getCurrentAccount()->getCode();
    }

    /**
     * @return string
     */
    public function getSavePrefixUrl()
    {
        return $this->getUrl('*/account/saveCouponPrefix');
    }

    /**
     * @return string
     */
    public function getSaveAffiliateDiscountUrl()
    {
        return $this->getUrl('*/account/saveDiscount');
    }

    /**
     * @return bool
     */
    public function hasCouponCode()
    {
        try {
            foreach ($this->getCampaigns() as $campaign) {
                if ($campaign->getCouponCode()) {
                    return true;
                }
            }
        } catch (LocalizedException $e) {
            $this->_logger->critical($e->getMessage());

            return false;
        }

        return false;
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getCampaigns()
    {
        $affiliateGroupId = $this->_affiliateHelper->getCurrentAffiliate()->getGroupId() ?: null;
        $campaigns = $this->campaignFactory->create()->getCollection();
        $campaigns->getAvailableCampaign($affiliateGroupId, $this->_storeManager->getWebsite()->getId());
        if (!$this->isAffiliateLogin()) {
            $campaigns->addFieldToFilter('display', Display::ALLOW_GUEST);
        }

        return $campaigns;
    }

    /**
     * @return bool
     */
    public function isAffiliateLogin()
    {
        $affAccount = $this->accountFactory->create()->load($this->customerSession->getCustomerId(), 'customer_id');

        return $affAccount->getId();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

    public function getCommissionDiscount($campaignId)
    {
        $result = [];
        foreach (self::COMMISSION_DISCOUNT_KEY as $key) {
            $result[$key] = 0;
        }

        try {
            $affiliateId = $this->isAffiliateLogin();
            $collectionCommissionDiscount = $this->collectionFactory->create();
            $collectionCommissionDiscount->addFieldToFilter('campaign_id', ['eq' => $campaignId]);
            $collectionCommissionDiscount->addFieldToFilter('affiliate_account_id', ['eq' => $affiliateId]);
            $commissionDiscount = $collectionCommissionDiscount->getFirstItem();

            if ($commissionDiscount && $commissionDiscount->getId()) {
                foreach (self::COMMISSION_DISCOUNT_KEY as $key) {
                    $result[$key] = round($commissionDiscount->getData($key), 2);
                }
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return $result;
    }
}
