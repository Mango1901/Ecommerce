<?php

namespace Magenest\AdminActivity\Model\ResourceModel\ActivityLogDetail;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Magenest\AdminActivity\Model\ResourceModel\ActivityLogDetail
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
			'Magenest\AdminActivity\Model\ActivityLogDetail',
			'Magenest\AdminActivity\Model\ResourceModel\ActivityLogDetail'
		);
	}
}
