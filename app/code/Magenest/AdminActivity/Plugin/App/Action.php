<?php

namespace Magenest\AdminActivity\Plugin\App;

/**
 * Class Action
 *
 * @package Magenest\AdminActivity\Plugin\App
 */
class Action
{
	/**
	 * @var \Magenest\AdminActivity\Model\Processor
	 */
	public $processor;

	/**
	 * @var \Magenest\AdminActivity\Helper\Benchmark
	 */
	public $benchmark;

	/**
	 * Action constructor.
	 *
	 * @param \Magenest\AdminActivity\Model\Processor $processor
	 * @param \Magenest\AdminActivity\Helper\Benchmark $benchmark
	 */
	public function __construct(
		\Magenest\AdminActivity\Model\Processor $processor,
		\Magenest\AdminActivity\Helper\Benchmark $benchmark
	) {
		$this->processor = $processor;
		$this->benchmark = $benchmark;
	}

	/**
	 * Get before dispatch data
	 *
	 * @param \Magento\Framework\Interception\InterceptorInterface $controller
	 * @return void
	 */
	public function beforeDispatch(\Magento\Framework\Interception\InterceptorInterface $controller)
	{
		$this->benchmark->start(__METHOD__);
		$actionName     = $controller->getRequest()->getActionName();
		$fullActionName = $controller->getRequest()->getFullActionName();

		$this->processor->init($fullActionName, $actionName);
		$this->processor->addPageVisitLog($controller->getRequest()->getModuleName());
		$this->benchmark->end(__METHOD__);
	}
}
