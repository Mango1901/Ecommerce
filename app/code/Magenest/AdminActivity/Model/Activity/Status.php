<?php

namespace Magenest\AdminActivity\Model\Activity;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Status
 *
 * @package Magenest\AdminActivity\Model\Activity
 */
class Status extends AbstractModel
{
	/**
	 * @var Int
	 */
	const ACTIVITY_NONE = 0;
	/**
	 * @var Int
	 */
	const ACTIVITY_REVERTABLE = 1;
	/**
	 * @var Int
	 */
	const ACTIVITY_REVERT_SUCCESS = 2;
	/**
	 * @var Int
	 */
	const ACTIVITY_FAIL = 3;

	/**
	 * @var \Magenest\AdminActivity\Model\ActivityFactory
	 */
	public $activityFactory;

	/**
	 * Status constructor.
	 *
	 * @param \Magenest\AdminActivity\Model\ActivityFactory $activityFactory
	 */
	public function __construct(
		\Magenest\AdminActivity\Model\ActivityFactory $activityFactory
	) {
		$this->activityFactory = $activityFactory;
	}

	/**
	 * Set success revert status
	 *
	 * @param $activityId
	 * @return void
	 */
	public function markSuccess($activityId)
	{
		$activityModel = $this->activityFactory->create()->load($activityId);
		$activityModel->setIsRevertable(self::ACTIVITY_REVERT_SUCCESS);
		$activityModel->save();
	}

	/**
	 * Set fail revert status
	 *
	 * @param $activityId
	 * @return void
	 */
	public function markFail($activityId)
	{
		$activityModel = $this->activityFactory->create()->load($activityId);
		$activityModel->setIsRevertable(self::ACTIVITY_FAIL);
		$activityModel->save();
	}
}
