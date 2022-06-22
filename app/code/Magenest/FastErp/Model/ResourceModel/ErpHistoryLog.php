<?php

namespace Magenest\FastErp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class HistoryLog
 *
 * @package Magenest\FastErp\Model\ResourceModel
 */
class ErpHistoryLog extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('magenest_erp_history_log', 'entity_id');
    }
}
