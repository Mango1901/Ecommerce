<?php

namespace Magenest\AdminActivity\Api\Activity;

/**
 * Interface ModelInterface
 *
 * @package Magenest\AdminActivity\Api\Activity
 */
interface ModelInterface
{
	/**
	 * Get old data
	 *
	 * @param $model
	 * @return mixed
	 */
	public function getOldData($model);

	/**
	 * Get edit data
	 *
	 * @param $model
	 * @return mixed
	 */
	public function getEditData($model, $fieldArray);
}
