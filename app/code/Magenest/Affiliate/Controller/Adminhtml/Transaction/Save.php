<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Transaction;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Controller\Adminhtml\Transaction;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use RuntimeException;

/**
 * Class Save
 * @package Magenest\Affiliate\Controller\Adminhtml\Transaction
 */
class Save extends Transaction
{
    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param AccountFactory $accountFactory
     * @param TransactionFactory $transactionFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        AccountFactory $accountFactory,
        TransactionFactory $transactionFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->_accountFactory = $accountFactory;

        parent::__construct($context, $transactionFactory, $coreRegistry, $resultPageFactory);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('transaction')) {
            try {
                $affiliateAccount = $this->_accountFactory->create()->load($data['customer_id'], 'customer_id');
                if (!$affiliateAccount->getId()) {
                    throw new LocalizedException(__('Account balance is not enough to create this transaction.'));
                }

                $transaction = $this->_transactionFactory->create()->createTransaction(
                    'admin',
                    $affiliateAccount,
                    new DataObject($data),
                    ['admin_id' => $this->_auth->getUser()->getId()]
                );

                $this->_getSession()->unsetData('transaction_customer_id');
                $this->messageManager->addSuccess(__('The Transaction has been created.'));
                $this->_getSession()->setAffiliateTransactionData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('affiliate/transaction/view', ['id' => $transaction->getId()]);

                    return $resultRedirect;
                }

                $resultRedirect->setPath('affiliate/*/');

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Transaction.'));
            }
            $this->_getSession()->setAffiliateTransactionData($data);
            $resultRedirect->setPath('affiliate/transaction/create');

            return $resultRedirect;
        }

        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }
}
