<?php

namespace Magenest\AdminActivity\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoadAfter
 *
 * @package Magenest\AdminActivity\Observer
 */
class LoadAfter implements ObserverInterface
{
	/**
	 * @var \Magenest\AdminActivity\Model\Processor
	 */
	private $processor;

	/**
	 * @var \Magenest\AdminActivity\Helper\Data
	 */
	public $helper;

	/**
	 * @var \Magenest\AdminActivity\Helper\Benchmark
	 */
	public $benchmark;

	/**
	 * LoadAfter constructor.
	 *
	 * @param \Magenest\AdminActivity\Model\Processor $processor
	 * @param \Magenest\AdminActivity\Helper\Data $helper
	 * @param \Magenest\AdminActivity\Helper\Benchmark $benchmark
	 */
	public function __construct(
		\Magenest\AdminActivity\Model\Processor $processor,
		\Magenest\AdminActivity\Helper\Data $helper,
		\Magenest\AdminActivity\Helper\Benchmark $benchmark
	) {
		$this->processor = $processor;
		$this->helper    = $helper;
		$this->benchmark = $benchmark;
	}

	/**
	 * Delete after
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return \Magento\Framework\Event\Observer
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$this->benchmark->start(__METHOD__);
		if (!$this->helper->isEnable()) {
			return $observer;
		}
		$object = $observer->getEvent()->getObject();
		$this->processor->modelLoadAfter($object);
		$this->benchmark->end(__METHOD__);
	}
}
