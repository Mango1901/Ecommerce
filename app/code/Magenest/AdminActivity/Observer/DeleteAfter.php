<?php

namespace Magenest\AdminActivity\Observer;

use Magenest\AdminActivity\Helper\Data as Helper;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class DeleteAfter
 *
 * @package Magenest\AdminActivity\Observer
 */
class DeleteAfter implements ObserverInterface
{
	/**
	 * @var string
	 */
	const SYSTEM_CONFIG = 'adminhtml_system_config_save';

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
	 * DeleteAfter constructor.
	 *
	 * @param \Magenest\AdminActivity\Model\Processor $processor
	 * @param Helper $helper
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
		if ($this->processor->validate($object) && ($this->processor->initAction == self::SYSTEM_CONFIG)) {
			$this->processor->modelEditAfter($object);
		}
		$this->processor->modelDeleteAfter($object);
		$this->benchmark->end(__METHOD__);
	}
}
