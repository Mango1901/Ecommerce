<?php

namespace Magenest\AdminActivity\Api;

/**
 * Interface ActivityRepositoryInterface
 *
 * @package Magenest\AdminActivity\Api
 */
interface ActivityRepositoryInterface
{
	/**
	 * Array of protected fields
	 *
	 * @return mixed
	 */
	public function protectedFields();

	/**
	 * Get collection of admin activity
	 *
	 * @return mixed
	 */
	public function getList();

	/**
	 * Get all admin activity data before date
	 *
	 * @param $endDate
	 * @return mixed
	 */
	public function getListBeforeDate($endDate);

	/**
	 * Remove activity log entry
	 *
	 * @param $activityId
	 * @return mixed
	 */
	public function deleteActivityById($activityId);

	/**
	 * Get all admin activity detail by activity id
	 *
	 * @param $activityId
	 * @return mixed
	 */
	public function getActivityDetail($activityId);

	/**
	 * Get all admin activity log by activity id
	 *
	 * @param $activityId
	 * @return mixed
	 */
	public function getActivityLog($activityId);

	/**
	 * Revert last changes made in module
	 *
	 * @param $activity
	 * @return mixed
	 */
	public function revertActivity($activity);

	/**
	 * Get old data for system config module
	 *
	 * @param $model
	 * @return mixed
	 */
	public function getOldData($model);

	/**
	 * Get admin activity by id
	 *
	 * @param $activityId
	 * @return mixed
	 */
	public function getActivityById($activityId);

	/**
	 * Check field is protected or not
	 *
	 * @param $fieldName
	 * @return mixed
	 */
	public function isFieldProtected($fieldName);
}
