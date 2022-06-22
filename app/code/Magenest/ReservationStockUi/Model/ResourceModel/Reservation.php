<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ReservationStockUi extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ReservationStockUi
 */

namespace Magenest\ReservationStockUi\Model\ResourceModel;

class Reservation extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const RESERVATION_TABLE = 'inventory_reservation';

    protected $_idFieldName = 'reservation_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::RESERVATION_TABLE, $this->_idFieldName);
    }
}
