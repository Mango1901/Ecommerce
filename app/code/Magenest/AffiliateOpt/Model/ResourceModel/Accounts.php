<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel;

use Exception;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\ResourceModel\Account;
use Psr\Log\LoggerInterface;

/**
 * Class Accounts
 * @package Magenest\AffiliateOpt\Model\ResourceModel
 */
class Accounts extends Account
{
    /**
     * @var AccountManagementInterface
     */
    protected $customerManagement;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagementInterface;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @param File $file
     * @param Data $helper
     * @param Context $context
     * @param Storage $imageStorage
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     * @param ManagerInterface $eventManager
     * @param AccountManagementInterface $accountManagement
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param GroupManagementInterface $groupManagementInterface
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        File $file,
        Data $helper,
        Context $context,
        Storage $imageStorage,
        Filesystem $filesystem,
        LoggerInterface $logger,
        ManagerInterface $eventManager,
        AccountManagementInterface $accountManagement,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        GroupManagementInterface $groupManagementInterface,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->_helper = $helper;
        $this->customerManagement = $accountManagement;
        $this->customerDataFactory = $customerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->groupManagementInterface = $groupManagementInterface;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($file, $helper, $context, $imageStorage, $filesystem, $logger, $eventManager);
    }

    /**
     * @param $data
     *
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function createCustomer($data)
    {
        $customer = $this->customerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customer,
            [
                'email' => $data['email'],
                'website_id' => $data['website_id'],
                'firstname' => 'firstname',
                'lastname' => 'lastname',
                'group' => $this->groupManagementInterface->getDefaultGroup($data['store_id']),
                'sendemail_store_id' => $data['store_id']
            ],
            CustomerInterface::class
        );

        return $this->customerManagement->createAccount($customer);
    }

    /**
     * @param array $entityData
     *
     * @return $this
     * @throws Exception
     */
    public function saveEntityFinish(array $entityData)
    {
        $this->beginTransaction();
        try {
            foreach ($entityData as $key => $data) {
                $customer = $this->customerRepositoryInterface->get($data['email']);
                if (!$customer->getId()) {
                    $customer = $this->createCustomer($data);
                }

                $entityData[$key]['customer_id'] = $customer->getId();
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }
}
