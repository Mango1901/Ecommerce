<?php

namespace Magenest\Affiliate\Helper;

use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\RewardPoints\Api\GetReferralCodeByCustomerInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Affiliate\Model\ResourceModel\Account\CollectionFactory;
use Magenest\Affiliate\Model\ResourceModel\Account\Collection;

class AccountHelper extends Data
{
    /**
     * @var CollectionFactory
     */
    private $collection;

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param AccountFactory $accountFactory
     * @param CampaignFactory $campaignFactory
     * @param TransactionFactory $transactionFactory
     * @param BlockFactory $blockFactory
     * @param CustomerFactory $customerFactory
     * @param CookieManagerInterface $cookieManagerInterface
     * @param CustomerSession $customerSession
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param CustomerViewHelper $customerViewHelper
     * @param LayoutInterface $layout
     * @param Registry $registry
     * @param CollectionFactory $collection
     * @param GetReferralCodeByCustomerInterface $getReferralCodeByCustomer
     */
    public function __construct(
        Context                $context,
        ObjectManagerInterface $objectManager,
        AccountFactory         $accountFactory,
        CampaignFactory        $campaignFactory,
        TransactionFactory     $transactionFactory,
        BlockFactory           $blockFactory,
        CustomerFactory        $customerFactory,
        CookieManagerInterface $cookieManagerInterface,
        CustomerSession        $customerSession,
        CookieMetadataFactory  $cookieMetadataFactory,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface  $storeManager,
        TransportBuilder       $transportBuilder,
        CustomerViewHelper     $customerViewHelper,
        LayoutInterface        $layout,
        Registry               $registry,
        CollectionFactory $collection,
        GetReferralCodeByCustomerInterface $getReferralCodeByCustomer
    ) {
        $this->collection = $collection;
        parent::__construct(
            $context,
            $objectManager,
            $accountFactory,
            $campaignFactory,
            $transactionFactory,
            $blockFactory,
            $customerFactory,
            $cookieManagerInterface,
            $customerSession,
            $cookieMetadataFactory,
            $priceCurrency,
            $storeManager,
            $transportBuilder,
            $customerViewHelper,
            $layout,
            $registry,
            $getReferralCodeByCustomer
        );
    }

    /**
     * @param $kangarooEmployeeId
     * @param null $accountId
     * @return array|bool[]
     */
    public function verifyKangarooEmployeeId($kangarooEmployeeId, $accountId = null)
    {
        if ($kangarooEmployeeId) {
            /**
             * @var Collection $affiliateCollection
             */
            $affiliateCollection = $this->collection->create()
                ->addFieldToFilter('employee_id', ['eq' => $kangarooEmployeeId])
                ->addFieldToFilter('account_id', ['neq' => $accountId]);

            if ($affiliateCollection->getSize()) {
                $affiliateAccount = $affiliateCollection->getFirstItem();
                return [
                    'pass' => false,
                    'duplicateAccount' => $affiliateAccount->getId()
                ];
            }
        }

        return [
            'pass' => true,
        ];
    }
}
