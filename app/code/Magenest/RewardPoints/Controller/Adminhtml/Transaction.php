<?php

namespace Magenest\RewardPoints\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magenest\RewardPoints\Model\TransactionFactory;

/**
 * Class Transaction
 * @package Magenest\RewardPoints\Controller\Adminhtml
 */
abstract class Transaction extends Action
{
    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Transaction constructor.
     *
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param TransactionFactory $transactionFactory
     * @param Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        TransactionFactory $transactionFactory,
        Registry $registry
    ) {
        $this->_pageFactory        = $pageFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_coreRegistry       = $registry;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magenest_RewardPoints::system_rewardpoints_points_transaction')
            ->addBreadcrumb(__('Transaction History Manager'), __('Transaction History Manager'));

        $resultPage->getConfig()->getTitle()->set(__('Transaction History Manager'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_RewardPoints::system_rewardpoints_points_transaction');
    }
}
