<?php

namespace Magenest\RewardPoints\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magenest\RewardPoints\Model\RuleFactory;

/**
 * Class Rule
 * @package Magenest\RewardPoints\Controller\Adminhtml
 */
abstract class Rule extends Action
{
    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Rule constructor.
     *
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param RuleFactory $ruleFactory
     * @param Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        RuleFactory $ruleFactory,
        Registry $registry
    ) {
        $this->_pageFactory  = $pageFactory;
        $this->_ruleFactory  = $ruleFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magenest_RewardPoints::system_rewardpoints_earning_rule')
            ->addBreadcrumb(__('Rules Manager'), __('Rules Manager'));

        $resultPage->getConfig()->getTitle()->set(__('Rules Manager'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_RewardPoints::system_rewardpoints_earning_rule');
    }
}
