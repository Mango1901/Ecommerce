<?php


namespace Magenest\Affiliate\Model;

use Magenest\Affiliate\Api\ConfigRepositoryInterface;
use Magenest\Affiliate\Api\Data\ConfigInterfaceFactory as ConfigAPIFactory;
use Magenest\Affiliate\Api\Data\Config\GeneralConfigInterface;
use Magenest\Affiliate\Api\Data\Config\AccountConfigInterface;
use Magenest\Affiliate\Api\Data\Config\CommissionConfigInterface;
use Magenest\Affiliate\Api\Data\Config\CommissionProcessConfigInterface;
use Magenest\Affiliate\Api\Data\Config\WithdrawConfigInterface;
use Magenest\Affiliate\Api\Data\Config\EmailConfigInterface;
use Magenest\Affiliate\Api\Data\Config\EmailAdminConfigInterface;
use Magenest\Affiliate\Api\Data\Config\EmailAccountConfigInterface;
use Magenest\Affiliate\Api\Data\Config\EmailTransactionConfigInterface;
use Magenest\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface;
use Magenest\Affiliate\Api\Data\Config\ReferConfigInterface;
use Magenest\Affiliate\Api\Data\Config\ReferSharingContentInterface;
use Magenest\Affiliate\Helper\Data;

/**
 * Class ConfigRepository
 * @package Magenest\Affiliate\Model
 */
class ConfigRepository implements ConfigRepositoryInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ConfigAPIFactory
     */
    protected $configAPIFactory;

    /**
     * @var GeneralConfigInterface
     */
    protected $generalConfig;

    /**
     * @var AccountConfigInterface
     */
    protected $accountConfig;

    /**
     * @var CommissionConfigInterface
     */
    protected $commissionConfig;

    /**
     * @var CommissionProcessConfigInterface
     */
    protected $commissionProcessConfig;

    /**
     * @var WithdrawConfigInterface
     */
    protected $withdrawConfig;

    /**
     * @var EmailConfigInterface
     */
    protected $emailConfig;

    /**
     * @var EmailAdminConfigInterface
     */
    protected $emailAdminConfig;

    /**
     * @var EmailAccountConfigInterface
     */
    protected $emailAccountConfig;

    /**
     * @var EmailTransactionConfigInterface
     */
    protected $emailTransactionConfig;

    /**
     * @var EmailWithdrawConfigInterface
     */
    protected $emailWithdrawConfig;

    /**
     * @var ReferConfigInterface
     */
    protected $referConfig;

    /**
     * @var ReferSharingContentInterface
     */
    protected $referSharingContent;

    /**
     * @param Data $helperData
     * @param ConfigAPIFactory $configAPIFactory
     * @param GeneralConfigInterface $generalConfig
     * @param AccountConfigInterface $accountConfig
     * @param CommissionConfigInterface $commissionConfig
     * @param CommissionProcessConfigInterface $commissionProcessConfig
     * @param WithdrawConfigInterface $withdrawConfig
     * @param EmailConfigInterface $emailConfig
     * @param EmailAdminConfigInterface $emailAdminConfig
     * @param EmailAccountConfigInterface $emailAccountConfig
     * @param EmailTransactionConfigInterface $emailTransactionConfig
     * @param EmailWithdrawConfigInterface $emailWithdrawConfig
     * @param ReferConfigInterface $referConfig
     * @param ReferSharingContentInterface $referSharingContent
     */
    public function __construct(
        Data $helperData,
        ConfigAPIFactory $configAPIFactory,
        GeneralConfigInterface $generalConfig,
        AccountConfigInterface $accountConfig,
        CommissionConfigInterface $commissionConfig,
        CommissionProcessConfigInterface $commissionProcessConfig,
        WithdrawConfigInterface $withdrawConfig,
        EmailConfigInterface $emailConfig,
        EmailAdminConfigInterface $emailAdminConfig,
        EmailAccountConfigInterface $emailAccountConfig,
        EmailTransactionConfigInterface $emailTransactionConfig,
        EmailWithdrawConfigInterface $emailWithdrawConfig,
        ReferConfigInterface $referConfig,
        ReferSharingContentInterface $referSharingContent
    ) {
        $this->helperData              = $helperData;
        $this->configAPIFactory        = $configAPIFactory;
        $this->generalConfig           = $generalConfig;
        $this->accountConfig           = $accountConfig;
        $this->commissionConfig        = $commissionConfig;
        $this->commissionProcessConfig = $commissionProcessConfig;
        $this->withdrawConfig          = $withdrawConfig;
        $this->emailConfig             = $emailConfig;
        $this->emailAdminConfig        = $emailAdminConfig;
        $this->emailAccountConfig      = $emailAccountConfig;
        $this->emailTransactionConfig  = $emailTransactionConfig;
        $this->emailWithdrawConfig     = $emailWithdrawConfig;
        $this->referConfig             = $referConfig;
        $this->referSharingContent     = $referSharingContent;
    }

    /**
     * {@inheritDoc}
     */
    public function get($storeId = null)
    {
        $configs = $this->configAPIFactory->create();
        $configs->addData($this->getConfigData($storeId));

        return $configs;
    }

    /**
     * @param $storeId
     *
     * @return array
     */
    public function getConfigData($storeId)
    {
        return [
            'general'    => $this->getGeneralConfig($storeId),
            'account'    => $this->getAccountConfig($storeId),
            'commission' => $this->getCommissionConfig($storeId),
            'withdraw'   => $this->getWithdrawConfig($storeId),
            'email'      => $this->getEmailConfig($storeId),
            'refer'      => $this->getReferConfig($storeId)
        ];
    }

    /**
     * @param $storeId
     *
     * @return mixed
     */
    public function getGeneralConfig($storeId = null)
    {
        $data = [
            'enabled'            => $this->helperData->getConfigGeneral('enabled', $storeId),
            'enable_banner'      => $this->helperData->getConfigGeneral('enable_banner', $storeId),
            'expired_time'       => $this->helperData->getConfigGeneral('expired_time', $storeId),
            'overwrite_cookies'  => $this->helperData->getConfigGeneral('overwrite_cookies', $storeId),
            'use_code_as_coupon' => $this->helperData->getConfigGeneral('use_code_as_coupon', $storeId),
            'show_link'          => $this->helperData->getConfigGeneral('show_link', $storeId),
            'page_welcome'       => $this->helperData->getConfigGeneral('page/welcome', $storeId),
            'url_type'           => $this->helperData->getConfigGeneral('url/type', $storeId),
            'url_prefix'         => $this->helperData->getConfigGeneral('url/prefix', $storeId),
            'url_param'          => $this->helperData->getConfigGeneral('url/param', $storeId),
            'url_code_length'    => $this->helperData->getConfigGeneral('url/code_length', $storeId),
            'affiliate_group'    => $this->helperData->getConfigGeneral('affiliate_group', $storeId)
        ];

        return $this->generalConfig->addData($data);
    }

    /**
     * @param $storeId
     *
     * @return AccountConfigInterface
     */
    public function getAccountConfig($storeId = null)
    {
        $data = [
            'sign_up_default_group'              => $this->helperData->getDefaultGroup($storeId),
            'sign_up_admin_approved'             => $this->helperData->isAdminApproved($storeId),
            'sign_up_default_email_notification' => $this->helperData->getModuleConfig(
                'account/sign_up/default_email_notification',
                $storeId
            ),
            'term_conditions_enable'             => $this->helperData->isEnableTermsAndConditions($storeId),
            'term_conditions_checkbox_text'      => $this->helperData->getTermsAndConditionsCheckboxText($storeId),
            'term_conditions_title'              => $this->helperData->getTermsAndConditionsTitle($storeId),
            'term_conditions_html'               => $this->helperData->getTermsAndConditionsHtml($storeId),
            'term_conditions_default_checkbox'   => $this->helperData->isCheckedEmailNotification($storeId),
            'balance_limit'                      => $this->helperData->getModuleConfig(
                'account/balance/limit',
                $storeId
            ),
            'balance_negative'                   => $this->helperData->getModuleConfig(
                'account/balance/negative',
                $storeId
            ),
        ];

        return $this->accountConfig->addData($data);
    }

    /**
     * @param $storeId
     *
     * @return CommissionConfigInterface
     */
    public function getCommissionConfig($storeId = null)
    {
        $process = [
            'earn_commission_invoice' => $this->helperData->getEarnCommissionInvoiceAfter($storeId),
            'holding_days'            => $this->helperData->getCommissionHoldingDays($storeId),
            'refund'                  => $this->helperData->isProcessRefund($storeId),
        ];

        $commission = [
            'by_tax'   => $this->helperData->isEarnCommissionFromTax($storeId),
            'shipping' => $this->helperData->isEarnCommissionFromShipping($storeId),
            'process'  => $this->commissionProcessConfig->addData($process)
        ];

        return $this->commissionConfig->addData($commission);
    }

    /**
     * @param $storeId
     *
     * @return WithdrawConfigInterface
     */
    public function getWithdrawConfig($storeId = null)
    {
        $data = [
            'allow_request'   => $this->helperData->isAllowWithdrawRequest($storeId),
            'payment_method'  => $this->helperData->getPaymentMethod($storeId),
            'minimum_balance' => $this->helperData->getWithdrawMinimumBalance($storeId),
            'minimum'         => $this->helperData->getWithdrawMinimum($storeId),
            'maximum'         => $this->helperData->getWithdrawMaximum($storeId)
        ];

        return $this->withdrawConfig->addData($data);
    }

    /**
     * @param $storeId
     *
     * @return EmailConfigInterface
     */
    public function getEmailConfig($storeId = null)
    {
        $data = [
            'sender'      => $this->helperData->getModuleConfig('email/sender', $storeId),
            'admin'       => $this->getEmailAdminConfig($storeId),
            'account'     => $this->getEmailAccountConfig($storeId),
            'transaction' => $this->getEmailTransactionConfig($storeId),
            'withdraw'    => $this->getEmailWithdrawConfig($storeId)
        ];

        return $this->emailConfig->addData($data);
    }

    /**
     * @param $storeId
     *
     * @return EmailAdminConfigInterface
     */
    public function getEmailAdminConfig($storeId = null)
    {
        $data = [
            'emails_to'                 => $this->helperData->getModuleConfig('email/admin/emails_to', $storeId),
            'enable_sign_up'            => $this->helperData->getModuleConfig('email/admin/enable_sign_up', $storeId),
            'sign_up_template'          => $this->helperData->getModuleConfig('email/admin/sign_up_template', $storeId),
            'enable_withdraw_request'   => $this->helperData->getModuleConfig(
                'email/admin/enable_withdraw_request',
                $storeId
            ),
            'withdraw_request_template' => $this->helperData->getModuleConfig(
                'email/admin/withdraw_request_template',
                $storeId
            ),
        ];

        return $this->emailAdminConfig->addData($data);
    }

    /**
     * @param $storeId
     *
     * @return EmailAccountConfigInterface
     */
    public function getEmailAccountConfig($storeId = null)
    {
        $data = [
            'enable'                   => $this->helperData->getModuleConfig('email/account/enable', $storeId),
            'welcome'                  => $this->helperData->getModuleConfig('email/account/welcome', $storeId),
            'approve'                  => $this->helperData->getModuleConfig('email/account/approve', $storeId),
            'enable_rejection'         => $this->helperData->getModuleConfig(
                'email/account/enable_rejection',
                $storeId
            ),
            'rejection_template'       => $this->helperData->getModuleConfig(
                'email/account/rejection_template',
                $storeId
            ),
            'enable_status'            => $this->helperData->getModuleConfig('email/account/enable_status', $storeId),
            'status_template'          => $this->helperData->getModuleConfig('email/account/status_template', $storeId),
            'enable_withdraw_cancel'   => $this->helperData->getModuleConfig(
                'email/account/enable_withdraw_cancel',
                $storeId
            ),
            'withdraw_cancel_template' => $this->helperData->getModuleConfig(
                'email/account/withdraw_cancel_template',
                $storeId
            ),
        ];

        return $this->emailAccountConfig->addData($data);
    }

    /**
     * @param $storeId
     *
     * @return EmailTransactionConfigInterface
     */
    public function getEmailTransactionConfig($storeId = null)
    {
        $data = [
            'enable'         => $this->helperData->getModuleConfig('email/transaction/enable', $storeId),
            'update_balance' => $this->helperData->getModuleConfig('email/transaction/update_balance', $storeId),
        ];

        return $this->emailTransactionConfig->addData($data);
    }

    /**
     * @param $storeId
     *
     * @return EmailWithdrawConfigInterface
     */
    public function getEmailWithdrawConfig($storeId = null)
    {
        $data = [
            'enable'   => $this->helperData->getModuleConfig('email/withdraw/enable', $storeId),
            'complete' => $this->helperData->getModuleConfig('email/withdraw/complete', $storeId),
        ];

        return $this->emailWithdrawConfig->addData($data);
    }

    /**
     * @param $storeId
     *
     * @return ReferConfigInterface
     */
    public function getReferConfig($storeId = null)
    {
        $sharingContent = [
            'subject'       => $this->helperData->getModuleConfig('refer/sharing_content/subject', $storeId),
            'email_content' => $this->helperData->getModuleConfig('refer/sharing_content/email_content', $storeId),
        ];

        $data = [
            'enable'          => $this->helperData->getModuleConfig('refer/enable', $storeId),
            'account_sharing' => $this->helperData->getModuleConfig('refer/account_sharing', $storeId),
            'default_link'    => $this->helperData->getModuleConfig('refer/default_link', $storeId),
            'sharing_content' => $this->referSharingContent->addData($sharingContent)
        ];

        return $this->referConfig->addData($data);
    }
}
