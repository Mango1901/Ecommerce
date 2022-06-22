<?php
namespace Magenest\SocialLogin\Setup\Patch\Data;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class MigrateCustomerSocialLoginData
 * @package Magenest\SocialLogin\Setup\Patch\Data
 */
class DefaultPasswordSocialAccount implements DataPatchInterface
{
    /**
     * @var CollectionFactory
     */
    protected $customerCollection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * MigrateCustomerSocialLoginData constructor.
     *
     * @param CollectionFactory $customerCollection
     * @param LoggerInterface $logger
     * @param Encryptor $encryptor
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CollectionFactory               $customerCollection,
        LoggerInterface                 $logger,
        Encryptor                       $encryptor,
        CustomerRepositoryInterface     $customerRepository
    ) {
        $this->customerCollection = $customerCollection;
        $this->logger             = $logger;
        $this->encryptor          = $encryptor;
        $this->customerRepository = $customerRepository;
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
     * @return MigrateCustomerSocialLoginData|void
     * @throws LocalizedException
     */
    public function apply()
    {
        $customerCollection = $this->customerCollection->create()
            ->addAttributeToSelect('magenest_sociallogin_type')
            ->addAttributeToFilter('magenest_sociallogin_id', ['neq' => null])
            ->addFieldToFilter('password_hash', ['eq' => null]);
        /** @var Customer $customer */
        foreach ($customerCollection as $customer) {
            try {
                $customer->getEmail();
                if ($email = $customer->getEmail()) {
                    $emailHash = $this->encryptor->hash($email);
                    $this->customerRepository->save($customer, $emailHash);
                }
            } catch (Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }
    }
}
