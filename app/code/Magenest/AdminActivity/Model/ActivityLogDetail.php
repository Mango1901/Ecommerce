<?php

namespace Magenest\AdminActivity\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ActivityLogDetail
 *
 * @package Magenest\AdminActivity\Model
 */
class ActivityLogDetail extends AbstractModel
{
	/**
	 * @var string
	 */
	const ACTIVITYLOGDETAIL_ID = 'entity_id'; // We define the id fieldname

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('Magenest\AdminActivity\Model\ResourceModel\ActivityLogDetail');
	}
}
