<?php

namespace Magenest\AdminActivity\Controller\Adminhtml\Activity;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class Log
 *
 * @package Magenest\AdminActivity\Controller\Adminhtml\Activity
 */
class Log extends Action
{
	/**
	 * @var \Magento\Framework\Controller\Result\RawFactory
	 */
	public $resultRawFactory;

	/**
	 * @var \Magento\Framework\View\LayoutFactory
	 */
	public $layoutFactory;

	/**
	 * Log constructor.
	 *
	 * @param Context $context
	 * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
	 * @param \Magento\Framework\View\LayoutFactory $layoutFactory
	 */
	public function __construct(
		Context $context,
		\Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
		\Magento\Framework\View\LayoutFactory $layoutFactory
	) {
		$this->resultRawFactory = $resultRawFactory;
		$this->layoutFactory    = $layoutFactory;

		parent::__construct($context);
	}

	/**
	 * view action
	 *
	 * @return $this
	 */
	public function execute()
	{
		$content = $this->layoutFactory->create()
			->createBlock(
				\Magenest\AdminActivity\Block\Adminhtml\ActivityLogListing::class
			);

		/** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
		$resultRaw = $this->resultRawFactory->create();
		return $resultRaw->setContents($content->toHtml());
	}
}
