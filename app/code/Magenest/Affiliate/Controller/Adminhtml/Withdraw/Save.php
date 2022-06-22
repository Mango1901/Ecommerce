<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Withdraw;

use Exception;
use Magenest\Affiliate\Helper\Payment as PaymentHelper;
use Magenest\Affiliate\Model\AccountFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Controller\Adminhtml\Withdraw;
use Magenest\Affiliate\Model\Withdraw\Status;
use Magenest\Affiliate\Model\WithdrawFactory;
use RuntimeException;
use Zend_Filter_Input;

/**
 * Class Save
 * @package Magenest\Affiliate\Controller\Adminhtml\Withdraw
 */
class Save extends Withdraw
{
    /**
     * @var Date
     */
    protected $_dateFilter;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param WithdrawFactory $withdrawFactory
     * @param AccountFactory $accountFactory
     * @param Date $dateFilter
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        WithdrawFactory $withdrawFactory,
        AccountFactory $accountFactory,
        Date $dateFilter,
        PaymentHelper $paymentHelper
    ) {
        $this->_dateFilter = $dateFilter;
        $this->_accountFactory = $accountFactory;
        $this->paymentHelper = $paymentHelper;
        parent::__construct($context, $resultPageFactory, $coreRegistry, $withdrawFactory);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $data           = $this->getRequest()->getPost('withdraw');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $data     = $this->_filterData($data);
            if(!$this->isCustomFeeSatisfactionEpayPayment($data)) {
                $this->messageManager->addErrorMessage(__('The actual amount transferred to the customer must not be less than 10000'));
                $resultRedirect->setPath('affiliate/*/create');
                return $resultRedirect;
            }

            $withdraw = $this->_initWithdraw();

            $account = $this->_accountFactory->create()->loadByCustomerId($data['customer_id']);
            if (!$account || !$account->getId() || !$account->isActive()) {
                $this->messageManager->addErrorMessage(__('This account is no longer existed!'));
                $resultRedirect->setPath('affiliate/*/');
                return $resultRedirect;
            }

            $bankNo = $account->getBankNo();
            $accNo = $account->getAccNo();
            $accountName = $account->getAccountName();
            $accType = $account->getAccType();

            if (!$bankNo || !$accNo || !$accountName) {
                $this->messageManager->addErrorMessage(__('Please enter your bank information in the affiliate setting!'));

                $resultRedirect->setPath('affiliate/*/');
                return $resultRedirect;
            }

            $data['payment_method'] = "vnpt_epay";
            $data['acc_type'] = $accType;
            $data['bank_no'] = $bankNo;
            $data['acc_no'] = $accNo;
            $data['account_name'] = $accountName;
            $withdraw->setData($data);
            $withdraw->setAccount($account);

            $redirectBack = $this->getRequest()->getParam('id') ? 'edit' : 'create';

            if ($this->getRequest()->getParam('back')) {
                $withdraw->setStatus(Status::COMPLETE);
            }
            try {
                $this->paymentHelper->checkWithdrawAmount($withdraw);
                $withdraw->save();
                $result = $this->paymentHelper->handleDisbursementForThreshold($withdraw);

                if ($result->getStatus() === Status::COMPLETE) {
                    $this->messageManager->addSuccessMessage(__('The withdraw has been saved and payed successfully.'));
                } else if ($result->getStatus() === Status::FAILED) {
                    $this->messageManager->addErrorMessage(__('Error when payment! Please check error in log file'));
                } else {
                    $this->messageManager->addWarningMessage(__('Your request has been sent successfully.!'));
                }

                $this->_getSession()->setAffiliateWithdrawData(false);
                $this->_getSession()->unsetData('withdraw_customer_id');

                $resultRedirect->setPath('affiliate/*/');

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Withdraw.'));
            }
            $this->_getSession()->setAffiliateWithdrawData($data);

            $resultRedirect->setPath('affiliate/*/' . $redirectBack, ['_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }

    /**
     * filter values
     *
     * @param $data
     *
     * @return array|mixed|null
     */
    protected function _filterData($data)
    {
        $inputFilter = new Zend_Filter_Input(['requested_at' => $this->_dateFilter], [], $data);
        $data        = $inputFilter->getUnescaped();

        return $data;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isCustomFeeSatisfactionEpayPayment($data)
    {
        if (is_numeric($data['fee']) && is_numeric($data['amount']))
        {
            if (($data['amount'] - $data['fee']) < 10000) {
                return false;
            }
        }
        return true;
    }
}
