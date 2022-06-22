<?php
namespace Magenest\Affiliate\Controller\Account;

use Exception;
use Magenest\Affiliate\Controller\Account;
use Magenest\Affiliate\Helper\Data as DataHelper;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magenest\PaymentEPay\Api\Data\HandleVerifyBankAccountInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Psr\Log\LoggerInterface;
use Magenest\Affiliate\Helper\AccountHelper;

class Settingpost extends Account
{
    /** @var Filesystem */
    private $fileSystem;

    /** @var UploaderFactory */
    private $uploaderFactory;

    /** @var AdapterFactory */
    private $adapterFactory;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @var HandleVerifyBankAccountInterface
     */
    private $handleVerifyBankAccount;

    /**
     * @var AccountHelper
     */
    private $accountHelper;

    /**
     * Settingpost constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TransactionFactory $transactionFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param DataHelper $dataHelper
     * @param CustomerSession $customerSession
     * @param Registry $registry
     * @param Filesystem $fileSystem
     * @param LoggerInterface $logger
     * @param AdapterFactory $adapterFactory
     * @param UploaderFactory $uploaderFactory
     * @param HandleVerifyBankAccountInterface $handleVerifyBankAccount
     * @param AccountHelper $accountHelper
     */
    public function __construct(
        Context                          $context,
        PageFactory                      $resultPageFactory,
        TransactionFactory               $transactionFactory,
        AccountFactory                   $accountFactory,
        WithdrawFactory                  $withdrawFactory,
        DataHelper                       $dataHelper,
        CustomerSession                  $customerSession,
        Registry                         $registry,
        Filesystem                       $fileSystem,
        LoggerInterface                  $logger,
        AdapterFactory                   $adapterFactory,
        UploaderFactory                  $uploaderFactory,
        HandleVerifyBankAccountInterface $handleVerifyBankAccount,
        AccountHelper                    $accountHelper
    ) {
        $this->logger                  = $logger;
        $this->fileSystem              = $fileSystem;
        $this->adapterFactory          = $adapterFactory;
        $this->uploaderFactory         = $uploaderFactory;
        $this->handleVerifyBankAccount = $handleVerifyBankAccount;
        $this->accountHelper           = $accountHelper;
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
     * @return Page|ResponseInterface|void
     */
    public function execute()
    {
        $accountData = $this->getRequest()->getParam('account');
        $account     = $this->dataHelper->getCurrentAffiliate();
        if (!$account || !$account->getId()) {
            return $this->_redirect('*/*/');
        }

        try {
            $accType     = $accountData['acc_type'] ?? 0;
            $bankNo      = $accountData['bank_no'] ?? null;
            $accNo       = $accountData['acc_no'] ?? null;
            $accountName = $accountData['account_name'] ?? null;
            $idFront     = $this->getRequest()->getFiles('id_front');
            $idBack      = $this->getRequest()->getFiles('id_back');

            //$resultEpay = $this->handleVerifyBankAccount->execute($bankNo, $accNo, $accType, $accountName);
            $resultEpay = ['ResponseCode' => 200];
            if (isset($resultEpay['ResponseCode']) && $resultEpay['ResponseCode'] === 200) {
                $kangarooEmployeeId = $this->getRequest()->getParam('employee_id') ?? null;
                $resultVerify = $this->accountHelper->verifyKangarooEmployeeId($kangarooEmployeeId, $account->getId());

                if (!$resultVerify['pass']) {
                    $this->messageManager->addErrorMessage(__('Kangaroo employee id %1 is already being used in other account', $kangarooEmployeeId));
                    return $this->_redirect('*/account/setting');
                }

                $account->addData([
                    'email_notification' => $accountData['email_notification'] ?? 0,
                    'acc_type'           => $accType,
                    'bank_no'            => $bankNo,
                    'acc_no'             => $accNo,
                    'account_name'       => $accountName,
                    'telephone'          => $this->getRequest()->getParam('telephone'),
                    'employee_id'        => $this->getRequest()->getParam('employee_id'),
                    'id_number'          => $account->getIdNumber() ?? $this->getRequest()->getParam('id_number'),
                    'license_date'       => $account->getLicenseDate() && $account->getLicenseDate() != "0000-00-00"
                        ? $account->getLicenseDate()
                        : $this->getRequest()->getParam('license_date'),
                    'issued_by'          => $account->getIssuedBy() ?? $this->getRequest()->getParam('issued_by'),
                    'id_front'           => !empty($idFront['name'])
                        ? $this->uploadImage('id_front')
                        : $account->getIdFront(),
                    'id_back'            => !empty($idBack['name'])
                        ? $this->uploadImage('id_back')
                        : $account->getIdBack(),
                    'city'               => $this->getRequest()->getParam('city'),
                    'city_id'            => $this->getRequest()->getParam('city_id'),
                    'district'           => $this->getRequest()->getParam('district'),
                    'district_id'        => $this->getRequest()->getParam('district_id'),
                    'ward'               => $this->getRequest()->getParam('ward'),
                    'ward_id'            => $this->getRequest()->getParam('ward_id'),
                ])->save();
                $this->messageManager->addSuccessMessage(__('Saved successfully!'));
            } else {
                $this->messageManager->addErrorMessage(__('Please verify bank account and try again!'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the account.'));
        }

        return $this->_redirect('*/account/setting');
    }

    /**
     * @param $fileId
     *
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
            $mediaDirectory  = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            $destinationPath = $mediaDirectory->getAbsolutePath('affiliate');
            $result          = $uploader->save($destinationPath);
            if (!$result) {
                throw new LocalizedException(__('File cannot be saved to path: $1', $destinationPath));
            }

            return $result['file'];
        } catch (\Exception $e) {
            $this->logger->critical(
                $e->getMessage(),
                ['trace' => $e->getTraceAsString(), 'path' => $destinationPath ?? ""]
            );
            $this->messageManager->addExceptionMessage($e);
            return null;
        }
    }
}
