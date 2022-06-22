<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Withdraw;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\Affiliate\Model\Email;
use Magenest\Affiliate\Model\ResourceModel\Withdraw\CollectionFactory;
use Magenest\Affiliate\Model\Withdraw;
use Magenest\Affiliate\Helper\Data;
use Magento\Customer\Model\CustomerFactory;
use Magenest\Affiliate\Model\AccountFactory;

/**
 * Class MassCancel
 * @package Magenest\Affiliate\Controller\Adminhtml\Withdraw
 */
class MassCancel extends Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var AccountFactory
     */
    private $accountFactory;

    /**
     * MassCancel constructor.
     *
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Email $email
     * @param Data $helperData
     * @param CustomerFactory $customerFactory
     * @param AccountFactory $accountFactory
     * @param Context $context
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Email $email,
        Data $helperData,
        CustomerFactory $customerFactory,
        AccountFactory $accountFactory,
        Context $context
    ) {
        $this->_filter            = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->email              = $email;
        $this->helperData         = $helperData;
        $this->customerFactory    = $customerFactory;
        $this->accountFactory     = $accountFactory;

        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $cancel     = 0;
        foreach ($collection as $withdraw) {
            /** @var Withdraw $withdraw */
            try {
                $withdraw->cancel();
                $cancel++;

                $customer = $this->customerFactory->create()->load($withdraw->getCustomerId());
                $storeId  = $customer->getStoreId();
                if ($storeId && $this->helperData->isEnableWithdrawCancelEmail($storeId)) {
                    /** @var Customer $customer */
                    $account = $this->accountFactory->create()->load($withdraw->getAccountId());

                    if ($account->getData('email_notification')) {
                        $this->email->sendEmailTemplate(
                            $customer->getEmail(),
                            $customer->getName(),
                            Data::XML_PATH_EMAIL_ACCOUNT_WITHDRAW_CANCEL_TEMPLATE,
                            compact('customer', 'account'),
                            $storeId
                        );
                    }
                }
            } catch (Exception $e) {
                $this->messageManager->addError(
                    __($e->getMessage())
                );
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been cancelled.', $cancel));
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
