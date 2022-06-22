<?php

namespace Magenest\AdminActivity\Plugin\User;

/**
 * Class Delete
 *
 * @package Magenest\AdminActivity\Plugin\User
 */
class Delete
{
	/**
	 * @var \Magenest\AdminActivity\Helper\Benchmark
	 */
	public $benchmark;

	/**
	 * Delete constructor.
	 *
	 * @param \Magenest\AdminActivity\Helper\Benchmark $benchmark
	 */
	public function __construct(
		\Magenest\AdminActivity\Helper\Benchmark $benchmark
	) {
		$this->benchmark = $benchmark;
	}

	/**
	 * @param \Magento\User\Model\ResourceModel\User $user
	 * @param callable $proceed
	 * @param $object
	 * @return mixed
	 */
	public function aroundDelete(\Magento\User\Model\ResourceModel\User $user, callable $proceed, $object)
	{
		$this->benchmark->start(__METHOD__);
		$object->load($object->getId());

		$result = $proceed($object);
		$object->afterDelete();

		$this->benchmark->end(__METHOD__);
		return $result;
	}
}
