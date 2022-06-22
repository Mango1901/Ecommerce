<?php

namespace Magenest\AdminActivity\Api;

/**
 * Interface LoginRepositoryInterface
 *
 * @package Magenest\AdminActivity\Api
 */
interface LoginRepositoryInterface
{
	/**
	 * Set login data
	 *
	 * @param $status
	 * @param $type
	 * @return mixed
	 */
	public function addLog($status, $type);

	/**
	 * Get all admin activity data before date
	 *
	 * @param $endDate
	 * @return mixed
	 */
	public function getListBeforeDate($endDate);

	/**
	 * Set login user
	 *
	 * @param $user
	 * @return mixed
	 */
	public function setUser($user);
}
