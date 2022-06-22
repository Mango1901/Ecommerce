<?php


namespace Magenest\Affiliate\Model;

use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Affiliate\Helper\Data as HelperData;
use Psr\Log\LoggerInterface as PsrLogger;

/**
 * Class Email
 * @package Magenest\Affiliate\Model
 */
class Email
{
    /**
     * Configuration paths for email identities
     */
    const XML_PATH_AFFILIATE_EMAIL_IDENTITY = 'email/sender';

    /**
     * @var PsrLogger
     */
    protected $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var CustomerViewHelper
     */
    protected $customerViewHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AccountFactory
     */
    protected $account;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Email constructor.
     *
     * @param CustomerViewHelper $customerViewHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param PsrLogger $logger
     * @param AccountFactory $accountFactory
     * @param HelperData $helperData
     */
    public function __construct(
        CustomerViewHelper $customerViewHelper,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        PsrLogger $logger,
        AccountFactory $accountFactory,
        HelperData $helperData
    ) {
        $this->helperData         = $helperData;
        $this->storeManager       = $storeManager;
        $this->customerViewHelper = $customerViewHelper;
        $this->scopeConfig        = $scopeConfig;
        $this->transportBuilder   = $transportBuilder;
        $this->logger             = $logger;
        $this->account            = $accountFactory;
    }

    /**
     * @param $emailTo
     * @param $nameTo
     * @param $template
     * @param array $templateParams
     * @param null $storeId
     *
     * @return $this
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendEmailTemplate($emailTo, $nameTo, $template, $templateParams = [], $storeId = null)
    {
        $templateId = $this->scopeConfig->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);
        if (!$storeId) {
            $storeId = Store::DISTRO_STORE_ID;
        }
        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($this->getSender())
            ->addTo($emailTo, $nameTo)
            ->getTransport();

        $transport->sendMessage();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->helperData->getModuleConfig(self::XML_PATH_AFFILIATE_EMAIL_IDENTITY);
    }

    /**
     * @param $customer
     *
     * @throws LocalizedException
     */
    public function sendRegisterEmail($customer)
    {
        if ($customer->getId()) {
            $account               = $this->account->create()->load($customer->getId(), 'customer_id');
            $storeId               = $this->getWebsiteStoreId($customer, null);
            $store                 = $this->storeManager->getStore($customer->getStoreId());
            $data['customername']  = $customer->getName();
            $data['store']         = $store;
            $data['accountstatus'] = $account->getStatus();

            try {
                $this->sendEmailTemplate(
                    $customer->getEmail(),
                    $customer->getName(),
                    //                    self::XML_PATH_REGISTER_AFFILIATE_EMAIL_TEMPLATE,
                    self::XML_PATH_AFFILIATE_EMAIL_IDENTITY,
                    $data,
                    $storeId
                );
            } catch (MailException $e) {
                // If we are not able to send a new account email, this should be ignored
                $this->logger->critical($e);
            }
        }
    }

    /**
     * @param $customer
     * @param null $defaultStoreId
     *
     * @return mixed|null
     * @throws LocalizedException
     */
    protected function getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }

        return $defaultStoreId;
    }

    /**
     * @param $friendName
     * @param $friendEmail
     * @param array $params
     */
    public function sendReferEmail($friendName, $friendEmail, $params = [])
    {
        try {
            $this->sendEmailTemplate(
                $friendEmail,
                $friendName,
                //                self::XML_PATH_REFER_AFFILIATE_EMAIL_TEMPLATE,
                self::XML_PATH_AFFILIATE_EMAIL_IDENTITY,
                $params
            );
        } catch (MailException $e) {
            // If we are not able to send a new account email, this should be ignored
            $this->logger->critical($e);
        }
    }

    /**
     * @param $customerName
     * @param $customerEmail
     * @param array $params
     */
    public function updateBalanceEmail($customerName, $customerEmail, $params = [])
    {
        try {
            $this->sendEmailTemplate(
                $customerEmail,
                $customerName,
                //                self::XML_PATH_UPDATE_BALANCE_AFFILIATE_EMAIL_TEMPLATE,
                self::XML_PATH_AFFILIATE_EMAIL_IDENTITY,
                $params
            );
        } catch (MailException $e) {
            // If we are not able to send a new account email, this should be ignored
            $this->logger->critical($e);
        }
    }

    /**
     * @param array $vars
     * @param string $template
     *
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendEmailToAdmin($vars, $template)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $emailTo = explode(',', $this->helperData->getAdminEmails($storeId));
        if (count($emailTo)) {
            $this->sendEmailTemplate(
                $emailTo,
                'Admin',
                $template,
                $vars,
                $storeId
            );
        }
    }
}
