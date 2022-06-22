<?php
namespace Magenest\CustomInventoryReservation\Model\ResourceModel\Reservation;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'reservation_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magenest\CustomInventoryReservation\Model\Reservation::class, \Magenest\CustomInventoryReservation\Model\ResourceModel\Reservation::class);
    }

}
