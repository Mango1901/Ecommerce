<?php


namespace Magenest\Affiliate\Controller;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Helper\Data as DataHelper;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;

/**
 * Class Account
 * @package Magenest\Affiliate\Controller
 */
abstract class Account extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var WithdrawFactory
     */
    protected $withdrawFactory;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Account constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TransactionFactory $transactionFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param DataHelper $dataHelper
     * @param CustomerSession $customerSession
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TransactionFactory $transactionFactory,
        AccountFactory $accountFactory,
        WithdrawFactory $withdrawFactory,
        DataHelper $dataHelper,
        CustomerSession $customerSession,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->transactionFactory = $transactionFactory;
        $this->accountFactory = $accountFactory;
        $this->withdrawFactory = $withdrawFactory;
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
