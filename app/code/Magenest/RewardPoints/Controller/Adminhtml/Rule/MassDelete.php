<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magenest\RewardPoints\Model\RuleFactory;
use Magenest\RewardPoints\Controller\Adminhtml\Rule;

/**
 * Class MassDelete
 * @package Magenest\RewardPoints\Controller\Adminhtml\Rule
 */
class MassDelete extends Rule
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param RuleFactory $ruleFactory
     * @param Registry $registry
     * @param Filter $filter
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        RuleFactory $ruleFactory,
        Registry $registry,
        Filter $filter
    )
    {
        $this->_filter      = $filter;
        parent::__construct($context, $pageFactory, $ruleFactory, $registry);
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection  = $this->_filter->getCollection($this->_ruleFactory->create()->getCollection());
        $ruleDeleted = 0;
        foreach ($collection->getItems() as $rule) {
            $rule->delete();
            $ruleDeleted++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $ruleDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('rewardpoints/*/index');
    }
}
