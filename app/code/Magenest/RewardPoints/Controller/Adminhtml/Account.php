<?php

namespace Magenest\RewardPoints\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magenest\RewardPoints\Model\AccountFactory;

/**
 * Class Account
 * @package Magenest\RewardPoints\Controller\Adminhtml
 */
abstract class Account extends Action
{
    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Account constructor.
     *
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param AccountFactory $accountFactory
     * @param Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        AccountFactory $accountFactory,
        Registry $registry
    ) {
        $this->_pageFactory    = $pageFactory;
        $this->_accountFactory = $accountFactory;
        $this->_coreRegistry   = $registry;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magenest_RewardPoints::system_rewardpoints_account')
            ->addBreadcrumb(__('Points Manager'), __('Points Manager'));

        $resultPage->getConfig()->getTitle()->set(__('Points Manager'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_RewardPoints::system_rewardpoints_account');
    }
}
