<?php


namespace Magenest\Affiliate\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Authorization\Model\CompositeUserContext;
use Magento\Framework\UrlInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magenest\Affiliate\Api\AccountRepositoryInterface;
use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\Api\AccountFactory as AccountAPIFactory;
use Magenest\Affiliate\Api\Data\CampaignSearchResultInterfaceFactory as CampaignSearchResult;
use Magenest\Affiliate\Model\Withdraw\Method;
use Magenest\Affiliate\Api\Data\WithdrawInterface;
use Magenest\Affiliate\Api\Data\TransactionSearchResultInterfaceFactory as TransactionSearchResult;
use Magenest\Affiliate\Api\Data\WithdrawSearchResultInterfaceFactory as WithdrawSearchResult;
use Zend_Validate;
use Exception;

/**
 * Class AccountRepository
 * @package Magenest\Affiliate\Model
 */
class AccountRepository implements AccountRepositoryInterface
{
    const XML_PATH_REFER_EMAIL_TEMPLATE = 'affiliate/refer/account_sharing';

    /**
     * @var CompositeUserContext
     */
    protected $userContext;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var bool
     */
    protected $isStandard = false;

    /**
     * @var AccountAPIFactory
     */
    protected $accountAPIFactory;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var Method
     */
    protected $paymentMethod;

    /**
     * @var TransactionSearchResult
     */
    protected $transSearchResult = null;

    /**
     * @var WithdrawSearchResult
     */
    protected $withdrawSearchResult = null;

    /**
     * @var CampaignSearchResult
     */
    protected $campaignSearchResult;

    /**
     * @param Data $helperData
     * @param AccountAPIFactory $accountAPIFactory
     * @param CompositeUserContext $userContext
     * @param UrlInterface $url
     * @param Method $paymentMethod
     * @param TransactionSearchResult $transSearchResult
     * @param WithdrawSearchResult $withdrawSearchResult
     * @param CampaignSearchResult $campaignSearchResult
     */
    public function __construct(
        Data                    $helperData,
        AccountAPIFactory       $accountAPIFactory,
        CompositeUserContext    $userContext,
        UrlInterface            $url,
        Method                  $paymentMethod,
        TransactionSearchResult $transSearchResult,
        WithdrawSearchResult    $withdrawSearchResult,
        CampaignSearchResult    $campaignSearchResult
    )
    {
        $this->helperData = $helperData;
        $this->accountAPIFactory = $accountAPIFactory;
        $this->userContext = $userContext;
        $this->url = $url;
        $this->paymentMethod = $paymentMethod;
        $this->transSearchResult = $transSearchResult;
        $this->withdrawSearchResult = $withdrawSearchResult;
        $this->campaignSearchResult = $campaignSearchResult;
    }

    /**
     * {@inheritDoc}
     */
    public function get($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        $customerId = $this->getCustomerId();

        $account = $this->accountAPIFactory->create()->load($customerId, 'customer_id');

        if (!$account->getId()) {
            throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
        }

        return $account;
    }

    /**
     * {@inheritDoc}
     */
    public function subscribe($isSubscribe, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        if (is_null($isSubscribe)) {
            throw new InputException(__("isSubscribe is required"));
        }

        $affiliate = $this->getAffiliateAccount();

        if ($affiliate->getId()) {
            $affiliate->setEmailNotification($isSubscribe ? 1 : 0);
            $affiliate->save();

            return true;
        } else {
            throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function invite($contacts, $referUrl = null, $subject = null, $content = null, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        if (!$contacts) {
            throw new InputException(__("contacts is required"));
        }

        $contacts = explode(",", $contacts);

        $affiliate = $this->getAffiliateAccount();

        if (!$referUrl || strpos($this->url->getBaseUrl(), $referUrl) !== false) {
            $referUrl = $this->helperData->getSharingUrl() . $affiliate->getId();
        }

        if (strpos($referUrl, $this->helperData->getSharingParam()) === false) {
            $referUrl = $referUrl . $this->helperData->getSharingParam() . $affiliate->getId();
        }

        if (!$subject) {
            $subject = $this->helperData->getDefaultEmailSubject();
        }

        $store = $this->helperData->createObject(\Magento\Store\Model\StoreManagerInterface::class);
        $storeId = $storeId ?? 1;
        $content = $this->getEmailContent($content, $store->getStore($storeId)->getName(), $referUrl);

        $successEmails = $errorEmails = [];

        foreach ($contacts as $key => $email) {
            if (strpos($email, '<') !== false) {
                $name = substr($email, 0, strpos($email, '<'));
                $email = substr($email, strpos($email, '<') + 1);
            } else {
                $emailIdentify = explode('@', $email);
                $name = $emailIdentify[0];
            }

            $name = trim($name, '\'"');
            $email = trim(rtrim(trim($email), '>'));
            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    continue;
                }

                $this->helperData->sendEmailTemplate(
                    new DataObject(['name' => $name, 'email' => $email, 'refer_url' => $referUrl]),
                    self::XML_PATH_REFER_EMAIL_TEMPLATE,
                    ['message' => $content, 'subject' => $subject],
                    Data::XML_PATH_EMAIL_SENDER,
                    $storeId
                );
                $successEmails[] = $email;
            } catch (Exception $e) {
                $errorEmails[] = $email;
            }
        }

        return Data::jsonEncode([
            "success" => $successEmails,
            "fail" => $errorEmails
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function signup($email = null, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        $affiliate = $this->getAffiliateAccount();
        if ($affiliate->getId() && !$affiliate->isActive()) {
            return Data::jsonEncode(["status" => Status::INACTIVE]);
        }

        $data = [];
        $data['customer_id'] = $this->getCustomerId();
        $signUpConfig = $this->helperData->getAffiliateAccountSignUp();
        $data['group_id'] = $signUpConfig['default_group'];

        if ($email) {
            /** @var \Magenest\Affiliate\Model\Account $parent */
            $parent = $this->helperData->getAffiliateByEmailOrCode(strtolower(trim($email)));
            $data['parent'] = $parent->getId();
            $data['parent_email'] = $parent->getCustomer()->getEmail();
        }
        $data['status'] = $signUpConfig['admin_approved'] ? Status::NEED_APPROVED : Status::ACTIVE;
        $data['email_notification'] = $signUpConfig['default_email_notification'];

        try {
            $affiliate->addData($data)->save();
            if ($affiliate->getStatus() == Status::NEED_APPROVED) {
                return Data::jsonEncode(["status" => Status::NEED_APPROVED]);
            }

            return Data::jsonEncode(["status" => Status::ACTIVE, "affiliate_id" => $affiliate->getId()]);

        } catch (Exception $e) {
            return Data::jsonEncode(["message" => $e->getMessage()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createReferLink($url, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        if (!$url) {
            throw new InputException(__("url is required"));
        }

        $affiliate = $this->getAffiliateAccount();
        $param = $this->helperData->getSharingParam() . $affiliate->getId();

        return $url . $param;
    }

    /**
     * {@inheritDoc}
     */
    public function transactions(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        /** @var \Magenest\Affiliate\Model\ResourceModel\Transaction\Collection $searchResult */
        $searchResult = $this->transSearchResult->create();

        $searchResult->addFieldToFilter("customer_id", ["eq" => $this->getCustomerId()]);

        return $this->helperData->processGetList($searchCriteria, $searchResult);
    }

    /**
     * {@inheritDoc}
     */
    public function withdrawsHistory(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        /** @var \Magenest\Affiliate\Model\ResourceModel\Withdraw\Collection $searchResult */
        $searchResult = $this->withdrawSearchResult->create();

        $searchResult->addFieldToFilter("customer_id", ["eq" => $this->getCustomerId()]);

        return $this->helperData->processGetList($searchCriteria, $searchResult);
    }

    /**
     * {@inheritDoc}
     */
    public function withdraw(WithdrawInterface $data, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        if (empty($data->getAmount())) {
            throw new InputException(__('Amount required'));
        }
        if (empty($data->getPaymentMethod())) {
            throw new InputException(__('Payment method required'));
        }

        if ($data->getAmount() <= 0.001) {
            throw new InputException(__('Amount must great than zero'));
        }

        if ($data->getPaymentMethod()) {
            $paymentMethods = $this->paymentMethod->getOptionHash();
            if (!isset($paymentMethods[$data->getPaymentMethod()])) {
                throw new NoSuchEntityException(__('Payment method doesn\'t exist'));
            }

            if ($data->getPaymentMethod() == 'paypal') {
                if (empty($data->getPaypalEmail())) {
                    throw new InputException(__('Paypal email required'));
                }

                if (!filter_var($data->getPaypalEmail(), FILTER_VALIDATE_EMAIL)) {
                    throw new InputException(__('Invalid paypal email address.'));
                }
            }
        }
        /** @var \Magenest\Affiliate\Model\Account $account */
        $account = $this->getAffiliateAccount();
        if (!$account->getId()) {
            throw new NoSuchEntityException(__('Affiliate account doesn\'t exist'));
        }

        $data = [
            'customer_id' => $account->getCustomerId(),
            'amount' => $data->getAmount(),
            'fee' => $data->getFee(),
            'payment_method' => $data->getPaymentMethod(),
            'withdraw_description' => $data->getWithdrawDescription(),
            'offline_address' => $data->getOfflineAddress(),
            'banktranfer' => $data->getBanktranfer(),
            'paypal_email' => $data->getPaypalEmail(),
            'paypal_transaction_id' => $data->getPaypalTransactionId()
        ];
        try {
            $withdraw = $this->getWithdraw()->setData($data)->save();
        } catch (Exception $e) {
            throw new CouldNotSaveException((__('Could not save the withdraw: %1', $e->getMessage())));
        }

        return $withdraw->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function campaigns($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        $affiliate = $this->getAffiliateAccount();
        /** @var \Magenest\Affiliate\Model\ResourceModel\Campaign\Collection $campaign */
        $campaign = $this->campaignSearchResult->create();
        $campaign->addFieldToFilter("affiliate_group_ids", ["finset" => [$affiliate->getGroupId()]]);

        return $campaign;
    }

    /**
     * {@inheritDoc}
     */
    public function guestCampaigns($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = 1;
        }

        if (!$this->helperData->isEnabled($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        /** @var \Magenest\Affiliate\Model\ResourceModel\Campaign\Collection $campaign */
        $campaign = $this->campaignSearchResult->create();
        $campaign->addFieldToFilter("display", ["eq" => 1]);

        return $campaign;
    }

    /**
     * @return mixed
     */
    public function getWithdraw()
    {
        return ObjectManager::getInstance()->create('\Magenest\Affiliate\Model\Withdraw');
    }

    /**
     * @return Account
     */
    public function getAffiliateAccount()
    {
        $customerId = $this->userContext->getUserId();
        return $this->helperData->getAffiliateAccount($customerId, 'customer_id');
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->userContext->getUserId();
    }

    /**
     * @param null $content
     * @param null $storeName
     * @param null $referUrl
     * @param null $accountName
     * @return array|mixed|string|string[]|null
     */
    public function getEmailContent($content = null, $storeName = null, $referUrl = null, $accountName = null)
    {
        if (is_null($content)) {
            $content = $this->helperData->getDefaultEmailBody();
        }
        return str_replace([
            '{{store_name}}',
            '{{refer_url}}',
            '{{account_name}}'
        ], [
            $storeName,
            $referUrl,
            $accountName
        ], $content);
    }
}
