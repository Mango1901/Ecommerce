<?php

namespace Magenest\Affiliate\Controller\Adminhtml\Account;

use Exception;
use Magenest\Affiliate\Controller\Adminhtml\Account;
use Magenest\Affiliate\Helper\AccountHelper;
use Magenest\Affiliate\Model\Account\BankInfo;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Customer\Service\ObserverCustomerDataProvider;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Save extends Account
{
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var BankInfo
     */
    protected $_bankInfo;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ObserverCustomerDataProvider
     */
    private $observerCustomerDataProvider;

    /**
     * @var AccountHelper
     */
    private $accountHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param AccountFactory $accountFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CustomerFactory $customerFactory
     * @param BankInfo $bankInfo
     * @param Filesystem $fileSystem
     * @param LoggerInterface $logger
     * @param AdapterFactory $adapterFactory
     * @param UploaderFactory $uploaderFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param ObserverCustomerDataProvider $observerCustomerDataProvider
     * @param AccountHelper $accountHelper
     */
    public function __construct(
        Context                      $context,
        AccountFactory               $accountFactory,
        Registry                     $coreRegistry,
        PageFactory                  $resultPageFactory,
        CustomerFactory              $customerFactory,
        BankInfo                     $bankInfo,
        Filesystem                   $fileSystem,
        LoggerInterface              $logger,
        AdapterFactory               $adapterFactory,
        UploaderFactory              $uploaderFactory,
        CustomerRepositoryInterface  $customerRepository,
        ObserverCustomerDataProvider $observerCustomerDataProvider,
        AccountHelper                $accountHelper
    ) {
        $this->_customerFactory = $customerFactory;
        $this->logger = $logger;
        $this->_bankInfo = $bankInfo;
        $this->fileSystem = $fileSystem;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->customerRepository = $customerRepository;
        $this->observerCustomerDataProvider = $observerCustomerDataProvider;
        $this->accountHelper = $accountHelper;
        parent::__construct($context, $accountFactory, $coreRegistry, $resultPageFactory);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('account')) {
            $account = $this->_initAccount();
            $bankOptions = $this->_bankInfo->getRawOptions();
            if (isset($bankOptions[$data['bank_no']]['type'])) {
                $data['acc_type'] = in_array(0, $bankOptions[$data['bank_no']]['type']) !== false ? 0 : 1;
            } else {
                $data['acc_type'] = 0;
            }

            $kangarooEmployeeId = $data['employee_id'] ?? null;
            $resultVerify = $this->accountHelper->verifyKangarooEmployeeId($kangarooEmployeeId, $account->getId());
            if (!$resultVerify['pass']) {
                $this->messageManager->addErrorMessage(
                    __('Kangaroo employee id %1 is already being used in other account with ID %2',
                        $kangarooEmployeeId,
                        $resultVerify['duplicateAccount']
                    )
                );

                $resultRedirect->setPath('affiliate/*/edit', ['id' => $account->getId()]);

                return $resultRedirect;
            }

            $idFrontFile = $this->getRequest()->getFiles('id_front');
            $idBackFile = $this->getRequest()->getFiles('id_back');
            $data['id_front'] = !empty($idFrontFile['name']) ? $this->uploadImage('id_front') : $account->getIdFront();
            $data['id_back'] = !empty($idBackFile['name']) ? $this->uploadImage('id_back') : $account->getIdBack();
            $account->addData($data);

            $this->_eventManager->dispatch(
                'affiliate_account_prepare_save',
                ['account' => $account, 'action' => $this]
            );

            $customer = $this->_customerFactory->create()->load($account->getCustomerId());
            $accountCollection = $this->_accountFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $account->getCustomerId());
            $numOfAccount = $accountCollection->getSize();
            if (($account->getId() && $numOfAccount > 1) || (!$account->getId() && $numOfAccount > 0)) {
                $this->messageManager->addError(__(
                    'The customer "%1" has registered as an affiliate already.',
                    $customer->getEmail()
                ));
                $this->_getSession()->setData('affiliate_account_data', $data);

                $resultRedirect->setPath('affiliate/*/*', ['_current' => true]);

                return $resultRedirect;
            }

            $account->setData('parent', null);
            if (isset($data['parent']) && is_numeric($data['parent'])) {
                $parent = $this->_accountFactory->create()->load($data['parent']);
                if ($parent && $parent->getId()) {
                    $account->setData('parent', $parent->getId());
                    $parentEmail = $this->_customerFactory->create()->load($parent->getCustomerId())->getEmail();
                    $account->setParentEmail($parentEmail);
                } else {
                    $this->messageManager->addNoticeMessage(__('Cannot find account referred.'));
                }
            }

            try {
                $account->save();
                $this->updateCustomerGroup($account->getCustomerId(), $data['customer_group_id']);
                $this->_getSession()->unsetData('account_customer_id');
                $this->messageManager->addSuccess($account->isObjectNew() ? __('The Account has been created successfully.') : __('The Account has been saved successfully.'));
                $this->_getSession()->setData('affiliate_account_data', false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('affiliate/*/edit', ['id' => $account->getId()]);

                    return $resultRedirect;
                }

                $resultRedirect->setPath('affiliate/*/');

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Account.'));
            }
            $this->_getSession()->setData('affiliate_account_data', $data);
            $resultRedirect->setPath('affiliate/*/*', ['_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }

    /**
     * @param $fileId
     * @return string|null
     * @throws Exception
     */
    private function uploadImage($fileId)
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            $imageAdapter = $this->adapterFactory->create();
            $uploader->addValidateCallback('affiliate_image_upload', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            $destinationPath = $mediaDirectory->getAbsolutePath('affiliate');
            $result = $uploader->save($destinationPath);
            if (!$result) {
                throw new LocalizedException(__('File cannot be saved.'));
            }

            return $result['file'];
        } catch (Exception $e) {
            $this->logger->critical(
                $e->getMessage(),
                ['trace' => $e->getTraceAsString(), 'path' => $destinationPath ?? ""]
            );
            $this->messageManager->addExceptionMessage($e);
            return null;
        }
    }

    /**
     * Save Customer group
     *
     * @param int $customerId
     * @param int $groupId
     * @return void
     */
    public function updateCustomerGroup($customerId, int $groupId): void
    {
        $customer = $this->getCustomerById($customerId);
        if ($customer) {
            try {
                $customer->setGroupId($groupId);
                $this->observerCustomerDataProvider->setIgnoreValidateTelephoneNumber(true);
                $this->customerRepository->save($customer);
            } catch (LocalizedException $exception) {
                $this->logger->error($exception);
            }
        }
    }

    /**
     * Get Customer By Id
     * @param int $customerId
     * @return CustomerInterface|null
     */
    private function getCustomerById(int $customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (LocalizedException $exception) {
            $customer = null;
            $this->logger->error($exception);
        }

        return $customer;
    }
}
