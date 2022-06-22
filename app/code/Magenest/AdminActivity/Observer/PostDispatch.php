<?php

namespace Magenest\AdminActivity\Observer;

use Magenest\AdminActivity\Helper\Data as Helper;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class PostDispatch
 *
 * @package Magenest\AdminActivity\Observer
 */
class PostDispatch implements ObserverInterface
{
	/**
	 * @var \Magenest\AdminActivity\Model\Processor
	 */
	private $processor;

	/**
	 * @var Helper
	 */
	public $helper;

	/**
	 * @var \Magenest\AdminActivity\Helper\Benchmark
	 */
	public $benchmark;

	/**
	 * PostDispatch constructor.
	 *
	 * @param \Magenest\AdminActivity\Model\Processor $processor
	 * @param Helper $helper
	 * @param \Magenest\AdminActivity\Helper\Benchmark $benchmark
	 */
	public function __construct(
		\Magenest\AdminActivity\Model\Processor $processor,
		Helper $helper,
		\Magenest\AdminActivity\Helper\Benchmark $benchmark
	) {
		$this->processor = $processor;
		$this->helper    = $helper;
		$this->benchmark = $benchmark;
	}

	/**
	 * Post dispatch
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
		$this->processor->saveLogs();
		$this->benchmark->end(__METHOD__);
	}
}
