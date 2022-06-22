<?php

namespace Magenest\SocialLogin\Plugin\Customer\Model;

use Exception;
use Magenest\SocialLogin\Controller\AbstractConnect;
use Magenest\SocialLogin\Helper\SocialLogin;
use Magenest\SocialLogin\Model\ResourceModel\SocialAccount as SocialAccountResourceModel;
use Magenest\SocialLogin\Model\SocialAccountFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class AccountConfirmation
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SocialAccountFactory
     */
    private $socialAccountFactory;

    /**
     * @var SocialAccountResourceModel
     */
    private $socialAccountResourceModel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Registry $registry
     * @param CustomerRepositoryInterface $customerRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param SocialAccountFactory $socialAccountFactory
     * @param SocialAccountResourceModel $socialAccountResourceModel
     * @param LoggerInterface $logger
     */
    public function __construct(
        Registry                    $registry,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface        $scopeConfig,
        SocialAccountFactory        $socialAccountFactory,
        SocialAccountResourceModel  $socialAccountResourceModel,
        LoggerInterface             $logger
    ) {
        $this->registry = $registry;
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $scopeConfig;
        $this->socialAccountFactory = $socialAccountFactory;
        $this->socialAccountResourceModel = $socialAccountResourceModel;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Customer\Model\AccountConfirmation $subject
     * @param bool $result
     * @param int|null $websiteId
     * @param int|null $customerId
     * @param string $customerEmail
     * @return bool
     */
    public function afterIsConfirmationRequired(
        \Magento\Customer\Model\AccountConfirmation $subject,
                                                    $result,
                                                    $websiteId,
                                                    $customerId,
                                                    $customerEmail
    ) {
        try {
            // Ignore set confirmation token when crate account entity model when social login first time
            if ($this->registry->registry(SocialLogin::IGNORE_CONFIRMATION_EMAIL)) {
                return false;
            }

            // Ignore social login account required confirmation token when login manually from second time
            if (
                $customerId &&
                !$this->scopeConfig->isSetFlag(
                    AbstractConnect::XML_PATH_ENABLE_CONFIRMATION_REQUIRED,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                )
            ) {
                $socialAccount = $this->socialAccountFactory->create();
                $this->socialAccountResourceModel->load($socialAccount, $customerId, 'customer_id');

                if ($socialAccount && $socialAccount->getId()) {
                    return false;
                }
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }
}
