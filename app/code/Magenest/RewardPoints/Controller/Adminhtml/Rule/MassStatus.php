<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Framework\Exception\LocalizedException;
use Magenest\RewardPoints\Controller\Adminhtml\Rule;

/**
 * Class MassStatus
 * @package Magenest\RewardPoints\Controller\Adminhtml\Rule
 */
class MassStatus extends Rule
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
     * MassStatus constructor.
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
     * @throws LocalizedException
     */
    public function execute()
    {
        $status     = (int)$this->getRequest()->getParam('status');
        $collection = $this->_filter->getCollection($this->_ruleFactory->create()->getCollection());
        $total      = 0;

        try {
            foreach ($collection as $item) {
                $item->setData('status', $status)->save();
                $total++;
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $total));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('rewardpoints/*/index');
    }
}
