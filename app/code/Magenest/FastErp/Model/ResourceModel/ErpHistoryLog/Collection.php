<?php

namespace Magenest\FastErp\Model\ResourceModel\ErpHistoryLog;

use Magenest\FastErp\Model\ErpHistoryLog as ErpHistoryLogModel;
use Magenest\FastErp\Model\ResourceModel\ErpHistoryLog as ErpHistoryLogResouceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Magenest\FastErp\Model\ResourceModel\Activity
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
			ErpHistoryLogModel::class,ErpHistoryLogResouceModel::class
		);
	}
}
