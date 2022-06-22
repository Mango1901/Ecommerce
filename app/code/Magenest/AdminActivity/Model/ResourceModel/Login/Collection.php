<?php

namespace Magenest\AdminActivity\Model\ResourceModel\Login;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Magenest\AdminActivity\Model\ResourceModel\Login
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
			'Magenest\AdminActivity\Model\Login',
			'Magenest\AdminActivity\Model\ResourceModel\Login'
		);
	}
}
