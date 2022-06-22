<?php

namespace Magenest\AdminActivity\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ActivityLogDetail
 *
 * @package Magenest\AdminActivity\Model\ResourceModel
 */
class ActivityLogDetail extends AbstractDb
{
	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		// Table Name and Primary Key column
		$this->_init('magenest_activity_detail', 'entity_id');
	}
}
