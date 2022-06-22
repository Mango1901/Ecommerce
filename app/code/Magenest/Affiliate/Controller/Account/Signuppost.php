<?php
namespace Magenest\Affiliate\Controller\Account;

use Exception;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Controller\Account;
use Magenest\Affiliate\Helper\Data as DataHelper;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magenest\Affiliate\Model\Email;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Signuppost extends Account
{
    /** @var Email */
    private $email;

    /** @var Filesystem */
    private $fileSystem;

    /** @var UploaderFactory */
    private $uploaderFactory;

    /** @var AdapterFactory */
    private $adapterFactory;

    /**
     * Signuppost constructor.
     *
     * @param Context $context
     * @param Filesystem $fileSystem
     * @param PageFactory $resultPageFactory
     * @param AdapterFactory $adapterFactory
     * @param UploaderFactory $uploaderFactory
     * @param TransactionFactory $transactionFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param DataHelper $dataHelper
     * @param CustomerSession $customerSession
     * @param Registry $registry
     * @param Email $email
     */
    public function __construct(
        Context $context,
        Filesystem $fileSystem,
        PageFactory $resultPageFactory,
        AdapterFactory $adapterFactory,
        UploaderFactory $uploaderFactory,
        TransactionFactory $transactionFactory,
        AccountFactory $accountFactory,
        WithdrawFactory $withdrawFactory,
        DataHelper $dataHelper,
        CustomerSession $customerSession,
        Registry $registry,
        Email $email
    ) {
        $this->email = $email;
        $this->fileSystem = $fileSystem;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
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
     * @return Page|void
     * @throws \Exception
     */
    public function execute()
    {
        $account = $this->dataHelper->getCurrentAffiliate();
        if ($account && $account->getId()) {
            if (!$account->isActive()) {
                $this->messageManager->addNoticeMessage(__('Your account is not active. Please contact us.'));
            }
            $this->_redirect('*/*');

            return;
        }

        $postValue = $this->getRequest()->getPostValue();
        if ($this->dataHelper->isEnableTermsAndConditions() && !isset($postValue['terms'])) {
            $this->messageManager->addErrorMessage(__('You have to agree with term and conditions.'));
            $this->_redirect('*/*');

            return;
        }
        $data = [];

        $customer            = $this->customerSession->getCustomer();
        $data['customer_id'] = $customer->getId();
        $signUpConfig        = $this->dataHelper->getAffiliateAccountSignUp();
        $data['group_id']    = $signUpConfig['default_group'];

        if (isset($postValue['referred_by'])) {
            /** @var \Magenest\Affiliate\Model\Account $parent */
            $parent = $this->dataHelper->getAffiliateByEmailOrCode(strtolower(trim($postValue['referred_by'])));
            $data['parent']       = $parent->getId();
            $data['parent_email'] = $parent->getCustomer()->getEmail();
        }
        $data['status']             = $signUpConfig['admin_approved'] ? Status::NEED_APPROVED : Status::ACTIVE;
        $data['email_notification'] = $signUpConfig['default_email_notification'];
        $data['telephone']          = $postValue['telephone'];
        $data['employee_id']        = $postValue['employee_id'] ?? null;
        $data['id_number']          = $postValue['id_number'];
        $data['license_date']       = $postValue['license_date'];
        $data['issued_by']          = $postValue['issued_by'];
        $data['city']               = $postValue['city'];
        $data['city_id']            = $postValue['city_id'];
        $data['district']           = $postValue['district'];
        $data['district_id']        = $postValue['district_id'];
        $data['ward']               = $postValue['ward'];
        $data['ward_id']            = $postValue['ward_id'];
        $data['id_front']           = $this->uploadImage('id_front');
        $data['id_back']            = $this->uploadImage('id_back');

        try {
            $account->addData($data)->save();
            $messageSuccess = __('Congratulations! You have successfully registered.');
            if ($account->getStatus() == Status::NEED_APPROVED) {
                $messageSuccess = __('Congratulations! You have successfully registered. ' .
                'We will review your affiliate account and inform you once it\'s approved!');
            }

            $this->messageManager->addSuccessMessage($messageSuccess);
            $this->_redirect('*/*');
            if ($this->dataHelper->isEnableAffiliateSignUpEmail()) {
                $this->email->sendEmailToAdmin(['customer' => $customer], DataHelper::XML_PATH_EMAIL_SIGN_UP_TEMPLATE);
            }

            return;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the Account.'));
        }

        $this->_redirect('*/*/signup');
    }

    /**
     * @param $fileId
     * @return string
     * @throws Exception
     */
    private function uploadImage($fileId)
    {
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
            throw new LocalizedException(__('File cannot be saved to path: $1', $destinationPath));
        }

        return $result['file'];
    }
}
