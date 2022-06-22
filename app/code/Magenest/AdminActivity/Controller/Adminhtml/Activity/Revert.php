<?php

namespace Magenest\AdminActivity\Controller\Adminhtml\Activity;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class Revert
 *
 * @package Magenest\AdminActivity\Controller\Adminhtml\Activity
 */
class Revert extends Action
{
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	public $resultJsonFactory;

	/**
	 * @var \Magenest\AdminActivity\Model\Processor
	 */
	public $processor;

	/**
	 * Revert constructor.
	 *
	 * @param Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magenest\AdminActivity\Model\Processor $processor
	 */
	public function __construct(
		Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magenest\AdminActivity\Model\Processor $processor
	) {
		parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->processor         = $processor;
	}

	/**
	 * Revert action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function execute()
	{
		$activityId = $this->getRequest()->getParam('id');
		$result     = $this->processor->revertActivity($activityId);
		return $this->resultJsonFactory->create()->setData($result);
	}
}
