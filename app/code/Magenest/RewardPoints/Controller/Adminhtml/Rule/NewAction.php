<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Rule;

use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magenest\RewardPoints\Controller\Adminhtml\Rule;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;


/**
 * Class NewAction
 * @package Magenest\RewardPoints\Controller\Adminhtml\Rule
 */
class NewAction extends Rule
{
    /**
     * @var ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * NewAction constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param RuleFactory $ruleFactory
     * @param Registry $registry
     * @param ForwardFactory $forwardFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        RuleFactory $ruleFactory,
        Registry $registry,
        ForwardFactory $forwardFactory
    )
    {
        $this->_resultForwardFactory = $forwardFactory;
        parent::__construct($context, $pageFactory, $ruleFactory, $registry);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
