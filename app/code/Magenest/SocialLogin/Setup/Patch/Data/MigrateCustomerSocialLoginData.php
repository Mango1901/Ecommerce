<?php


namespace Magenest\SocialLogin\Setup\Patch\Data;


/**
 * Class MigrateCustomerSocialLoginData
 * @package Magenest\SocialLogin\Setup\Patch\Data
 */
class MigrateCustomerSocialLoginData implements \Magento\Framework\Setup\Patch\DataPatchInterface
{

    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory
     */
    protected $socialAccountCollection;
    /**
     * @var \Magenest\SocialLogin\Model\SocialAccountFactory
     */
    protected $socialAccountModel;
    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount
     */
    protected $socialAccountResource;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollection;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * MigrateCustomerSocialLoginData constructor.
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection
     * @param \Magenest\SocialLogin\Model\SocialAccountFactory $socialAccountModel
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection,
        \Magenest\SocialLogin\Model\SocialAccountFactory $socialAccountModel,
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->socialAccountCollection = $socialAccountCollection;
        $this->socialAccountModel = $socialAccountModel;
        $this->socialAccountResource = $socialAccountResource;
        $this->customerCollection = $customerCollection;
        $this->timezone = $timezone;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return \Magenest\SocialLogin\Setup\Patch\Data\MigrateCustomerSocialLoginData|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $customerCollection = $this->customerCollection->create()
                                                       ->addAttributeToSelect('magenest_sociallogin_type')
                                                       ->addAttributeToFilter('magenest_sociallogin_id',['neq' => null]);
        /** @var \Magento\Customer\Model\Customer $customer */
        foreach ($customerCollection as $customer) {
            $socialAccount = $this->socialAccountCollection->create()->addFieldToFilter('social_login_id',$customer->getData('magenest_sociallogin_id'))
                                                                     ->addFieldToFilter('social_login_type',$customer->getData('magenest_sociallogin_type'))
                                                                     ->getFirstItem();
            if ($socialAccount->getId()) {
                continue;
            } else {
                try {
                    $currentDateTime = $this->timezone->date()->format('Y-m-d H:i:s');
                    $socialAccount->setCustomerId($customer->getId());
                    $socialAccount->setSocialEmail($customer->getEmail());
                    $socialAccount->setSocialLoginId($customer->getData('magenest_sociallogin_id'));
                    $socialAccount->setSocialLoginType($customer->getData('magenest_sociallogin_type'));
                    $socialAccount->setCreatedAt($currentDateTime);
                    $socialAccount->setLastLogin($currentDateTime);
                    $this->socialAccountResource->save($socialAccount);
                } catch (\Exception $exception) {
                    $this->logger->critical($exception->getMessage());
                }
            }
        }
    }
}