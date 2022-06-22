<?php

namespace Magenest\AdminActivity\Observer;

use Magenest\AdminActivity\Api\ActivityRepositoryInterface;
use Magenest\AdminActivity\Helper\Data as Helper;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveBefore
 *
 * @package Magenest\AdminActivity\Observer
 */
class SaveBefore implements ObserverInterface
{
	/**
	 * @var Helper
	 */
	public $helper;

	/**
	 * @var \Magenest\AdminActivity\Model\Processor
	 */
	public $processor;

	/**
	 * @var ActivityRepositoryInterface
	 */
	public $activityRepository;

	/**
	 * @var \Magenest\AdminActivity\Helper\Benchmark
	 */
	public $benchmark;

	/**
	 * SaveBefore constructor.
	 *
	 * @param Helper $helper
	 * @param \Magenest\AdminActivity\Model\Processor $processor
	 * @param ActivityRepositoryInterface $activityRepository
	 * @param \Magenest\AdminActivity\Helper\Benchmark $banchmark
	 */
	public function __construct(
		Helper $helper,
		\Magenest\AdminActivity\Model\Processor $processor,
		ActivityRepositoryInterface $activityRepository,
		\Magenest\AdminActivity\Helper\Benchmark $benchmark
	) {
		$this->helper             = $helper;
		$this->processor          = $processor;
		$this->activityRepository = $activityRepository;
		$this->benchmark          = $benchmark;
	}

	/**
	 * Save before
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
		if ($object->getId() == 0) {
			$object->setCheckIfIsNew(true);
		} else {
			$object->setCheckIfIsNew(false);
			if ($this->processor->validate($object)) {
				$origData = $object->getOrigData();
				if (!empty($origData)) {
					return $observer;
				}
				$data = $this->activityRepository->getOldData($object);
				foreach ($data->getData() as $key => $value) {
					$object->setOrigData($key, $value);
				}
			}
		}
		$this->benchmark->end(__METHOD__);
		return $observer;
	}
}
