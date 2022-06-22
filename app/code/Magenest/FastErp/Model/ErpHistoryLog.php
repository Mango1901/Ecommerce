<?php

namespace Magenest\FastErp\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class HistoryLog
 *
 * @package Magenest\FastErp\Model
 */
class ErpHistoryLog extends AbstractModel
{
    const PRODUCT = 1;
    const ORDER = 2;
    const STOCK = 3;
    const WAREHOUSE = 4;

    /**
     * @var string
     */
    const HISTORY_LOG_ID = 'entity_id'; // We define the id field name

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\FastErp\Model\ResourceModel\ErpHistoryLog');
    }

    /**
     * @return array
     */
    public function getAvailableTypeErp()
    {
        return [
            self::PRODUCT => __('Product'),
            self::ORDER => __('Order'),
            self::STOCK => __('Stock Quantity'),
            self::WAREHOUSE => __('Warehouse'),
        ];
    }
}
