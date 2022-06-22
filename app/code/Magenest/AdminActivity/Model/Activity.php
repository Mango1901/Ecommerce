<?php

namespace Magenest\AdminActivity\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Activity
 *
 * @package Magenest\Activity\Model
 */
class Activity extends AbstractModel
{
	/**
	 * @var string
	 */
	const ACTIVITY_ID = 'entity_id'; // We define the id field name

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('Magenest\AdminActivity\Model\ResourceModel\Activity');
	}
}
