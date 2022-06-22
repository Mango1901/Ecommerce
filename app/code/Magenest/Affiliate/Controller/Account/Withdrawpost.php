<?php


namespace Magenest\Affiliate\Controller\Account;

use Exception;
use Magenest\Affiliate\Model\Withdraw\Status;
use Magenest\PaymentEPay\Api\Data\HandleDisbursementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Controller\Account;
use Magenest\Affiliate\Helper\Payment as PaymentHelper;
use Magenest\Affiliate\Helper\Data as DataHelper;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use RuntimeException;
use Magenest\Affiliate\Model\Email;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Withdrawpost
 *
 * @package Magenest\Affiliate\Controller\Account
 */
class Withdrawpost extends Account
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * Withdrawpost constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TransactionFactory $transactionFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param DataHelper $dataHelper
     * @param CustomerSession $customerSession
     * @param Registry $registry
     * @param Email $email
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TransactionFactory $transactionFactory,
        AccountFactory $accountFactory,
        WithdrawFactory $withdrawFactory,
        DataHelper $dataHelper,
        CustomerSession $customerSession,
        Registry $registry,
        Email $email,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        PaymentHelper $paymentHelper
    ) {
        $this->email         = $email;
        $this->storeManager  = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->paymentHelper = $paymentHelper;

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
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $account = $this->paymentHelper->getCurrentAffiliate();
        if (!$account || !$account->getId() || !$account->isActive()) {
            $this->messageManager->addNoticeMessage(__('An error occur. Please contact us.'));

            return $this->_redirect('*/*');
        }

        $bankNo = $account->getBankNo();
        $accNo = $account->getAccNo();
        $accountName = $account->getAccountName();
        $accType = $account->getAccType();

        if (!$bankNo || !$accNo || !$accountName) {
            $this->messageManager->addNoticeMessage(__('Please enter your bank information in the affiliate setting!'));

            return $this->_redirect('*/*/withdraw');
        }

        $customer  = $this->customerSession->getCustomer();
        $postValue = $this->getRequest()->getPostValue();

        $amount                 = $this->convertToBaseCurrency($postValue['amount']);
        $data                   = [];
        $data['customer_id']    = $customer->getId();
        $data['account_id']     = $account->getId();
        $data['amount']         = $amount;
        $data['payment_method'] = "vnpt_epay";
        $data['acc_type'] = $accType;
        $data['bank_no'] = $bankNo;
        $data['acc_no'] = $accNo;
        $data['account_name'] = $accountName;

        $this->customerSession->setWithdrawFormData($data);
        $withdraw = $this->withdrawFactory->create();
        $withdraw->addData($data)->setAccount($account);

        try {
            $this->paymentHelper->checkWithdrawAmount($withdraw);
            $withdraw->save();
            $result = $this->paymentHelper->handleDisbursementForThreshold($withdraw);

            if ($result->getStatus() === Status::COMPLETE) {
                $this->messageManager->addSuccessMessage(__('Your withdraw request has been approved successfully.'));
            } else if ($result->getStatus() === Status::FAILED) {
                $this->messageManager->addWarningMessage(__('Your withdraw request has been error. Please contact merchant for more information.'));
            } else {
                $this->messageManager->addWarningMessage(__('Your request has been sent successfully. We will review your request and inform you once it\'s approved!'));
            }

            $this->customerSession->setWithdrawFormData(false);
            if ($this->paymentHelper->isEnableWithdrawRequestEmail()) {
                $this->email->sendEmailToAdmin(
                    compact('customer', 'data'),
                    DataHelper::XML_PATH_EMAIL_WITHDRAW_REQUEST_TEMPLATE
                );
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the request.'));
        }

        return $this->_redirect('*/*/withdraw');
    }

    /**
     * @param float $currentPrice
     *
     * @return float
     * @throws NoSuchEntityException
     */
    public function convertToBaseCurrency($currentPrice)
    {
        $store = $this->storeManager->getStore();
        $rate  = $this->priceCurrency->convert($currentPrice, $store) / $currentPrice;

        return $currentPrice / $rate;
    }
}
