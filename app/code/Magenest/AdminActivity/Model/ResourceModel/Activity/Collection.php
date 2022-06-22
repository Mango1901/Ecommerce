<?php

namespace Magenest\AdminActivity\Model\ResourceModel\Activity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Magenest\AdminActivity\Model\ResourceModel\Activity
 */
class Collection extends AbstractCollection
{
	/**
	 * Define resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init(
			'Magenest\AdminActivity\Model\Activity',
			'Magenest\AdminActivity\Model\ResourceModel\Activity'
		);
	}
}
