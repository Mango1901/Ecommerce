<?php

namespace Magenest\AdminActivity\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Login
 *
 * @package Magenest\Activity\Model
 */
class Login extends AbstractModel
{
	/**
	 * @var string
	 */
	const LOGIN_ACTIVITY_ID = 'entity_id'; // We define the id field name

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('Magenest\AdminActivity\Model\ResourceModel\Login');
	}
}
