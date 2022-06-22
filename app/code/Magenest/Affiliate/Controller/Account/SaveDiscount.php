<?php

namespace Magenest\Affiliate\Controller\Account;

use Exception;
use Magenest\Affiliate\Controller\Account;
use Magenest\Affiliate\Helper\Data as DataHelper;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\CommissionDiscountFactory;
use Magenest\Affiliate\Model\ResourceModel\Campaign as CampaignResourceModel;
use Magenest\Affiliate\Model\ResourceModel\CommissionDiscount as CommissionDiscountResourceModel;
use Magenest\Affiliate\Model\ResourceModel\CommissionDiscount\CollectionFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Save custom discount affiliate for customer
 */
class SaveDiscount extends Account
{
    /**
     * @var CampaignFactory
     */
    private $campaign;

    /**
     * @var CampaignResourceModel
     */
    private $campaignResourceModel;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var CollectionFactory
     */
    private $collection;

    /**
     * @var CommissionDiscountResourceModel
     */
    private $commissionDiscountResourceModel;

    /**
     * @var CommissionDiscountFactory
     */
    private $commissionDiscount;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TransactionFactory $transactionFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param DataHelper $dataHelper
     * @param CustomerSession $customerSession
     * @param Registry $registry
     * @param CampaignFactory $campaign
     * @param CampaignResourceModel $campaignResourceModel
     * @param SerializerInterface $serializer
     * @param CommissionDiscountFactory $commissionDiscount
     * @param CommissionDiscountResourceModel $commissionDiscountResourceModel
     * @param CollectionFactory $collection
     */
    public function __construct(
        Context                         $context,
        PageFactory                     $resultPageFactory,
        TransactionFactory              $transactionFactory,
        AccountFactory                  $accountFactory,
        WithdrawFactory                 $withdrawFactory,
        DataHelper                      $dataHelper,
        CustomerSession                 $customerSession,
        Registry                        $registry,
        CampaignFactory                 $campaign,
        CampaignResourceModel           $campaignResourceModel,
        SerializerInterface             $serializer,
        CommissionDiscountFactory       $commissionDiscount,
        CommissionDiscountResourceModel $commissionDiscountResourceModel,
        CollectionFactory               $collection
    ) {
        $this->campaign = $campaign;
        $this->campaignResourceModel = $campaignResourceModel;
        $this->serializer = $serializer;
        $this->commissionDiscount = $commissionDiscount;
        $this->commissionDiscountResourceModel = $commissionDiscountResourceModel;
        $this->collection = $collection;
        parent::__construct(
            $context,
            $resultPageFactory,
            $transactionFactory,
            $accountFactory,
            $withdrawFactory,
            $dataHelper,
            $customerSession,
            $registry
        );
    }

    /**
     * @return Redirect|Page
     */
    public function execute()
    {
        $dataPost = $this->getRequest()->getParams();

        if (!isset($dataPost['campaign_id'])) {
            $this->messageManager->addErrorMessage(__('Value campaign does not exist campaign in submit form'));
            return $this->resultRedirectFactory->create()->setPath('*');
        }

        $campaign = $this->campaign->create();
        $this->campaignResourceModel->load($campaign, $dataPost['campaign_id']);

        if (!$campaign || !$campaign->getId()) {
            $this->messageManager->addErrorMessage(__('Value campaign from submit not correct'));
            return $this->resultRedirectFactory->create()->setPath('*');
        }

        try {
            $commission = $campaign->getCommission();
            if (is_array($commission) && isset($commission['tier_1'])) {
                $customerValue = $dataPost['customer_value'] ?? 0;
                $valueRemain = $dataPost['value'] ?? 0;
                $totalValue = $commission['tier_1']['value'] ?? 0;
                $customerSecondValue = $dataPost['customer_value'] ?? 0;
                $valueSecondRemain = $dataPost['value_second'] ?? 0;
                $totalSecondValue = $commission['tier_1']['value_second'] ?? 0;

                if (
                    ($customerValue + $valueRemain) != $totalValue &&
                    ($customerSecondValue + $valueSecondRemain) != $totalSecondValue
                ) {
                    $this->messageManager->addErrorMessage(__('Sum of customer discount and affiliate commission is not equal with total commission setup by admin'));
                    return $this->resultRedirectFactory->create()->setPath('*');
                }

                $currentAffiliate = $this->accountFactory->create()->load(
                    $this->customerSession->getCustomerId(),
                    'customer_id'
                );
                $currentAffiliateId = $currentAffiliate->getId();

                if ($currentAffiliateId) {
                    /**
                     * @var $collectionCommissionDiscount \Magenest\Affiliate\Model\ResourceModel\CommissionDiscount\Collection
                     */
                    $collectionCommissionDiscount = $this->collection->create();
                    $collectionCommissionDiscount->addFieldToFilter('campaign_id', ['eq' => $dataPost['campaign_id']]);
                    $collectionCommissionDiscount->addFieldToFilter('affiliate_account_id', ['eq' => $currentAffiliateId]);

                    if ($collectionCommissionDiscount->getFirstItem() && $collectionCommissionDiscount->getFirstItem()->getId()) {
                        $commissionDiscount = $collectionCommissionDiscount->getFirstItem();
                    } else {
                        $commissionDiscount = $this->commissionDiscount->create();
                    }

                    $commissionDiscount->addData([
                        'affiliate_account_id' => $currentAffiliateId,
                        'campaign_id' => $dataPost['campaign_id'],
                        'total_value' => $totalValue,
                        'type' => $commission['tier_1']['type'] ?? 1,
                        'total_value_second' => $totalSecondValue,
                        'type_second' => $commission['tier_1']['type_second'] ?? 1,
                        'customer_value' => $dataPost['customer_value'],
                        'customer_value_second' => $dataPost['customer_value_second'],
                    ]);

                    $this->commissionDiscountResourceModel->save($commissionDiscount);
                    $this->messageManager->addSuccessMessage(__('Update Successfully'));
                } else {
                    $this->messageManager->addErrorMessage(__('This account does not affiliate account'));
                }
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*');
    }
}
