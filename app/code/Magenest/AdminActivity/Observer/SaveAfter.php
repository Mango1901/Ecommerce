<?php

namespace Magenest\AdminActivity\Observer;

use Magenest\AdminActivity\Helper\Data as Helper;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveAfter
 *
 * @package Magenest\AdminActivity\Observer
 */
class SaveAfter implements ObserverInterface
{
	/**
	 * @var string
	 */
	const ACTION_MASSCANCEL = 'massCancel';
	/**
	 * @var string
	 */
	const SYSTEM_CONFIG = 'adminhtml_system_config_save';

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
	 * SaveAfter constructor.
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
	 * Save after
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return \Magento\Framework\Event\Observer|boolean
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$this->benchmark->start(__METHOD__);

		if (!$this->helper->isEnable()) {
			return $observer;
		}
		$object = $observer->getEvent()->getObject();
		if ($object->getCheckIfIsNew()) {
			if ($this->processor->initAction == self::SYSTEM_CONFIG) {
				$this->processor->modelEditAfter($object);
			}
			$this->processor->modelAddAfter($object);
		} else {
			if ($this->processor->validate($object)) {
				if ($this->processor->eventConfig['action'] == self::ACTION_MASSCANCEL) {
					$this->processor->modelDeleteAfter($object);
				}
				$this->processor->modelEditAfter($object);
			}
		}
		$this->benchmark->end(__METHOD__);
		return true;
	}
}
