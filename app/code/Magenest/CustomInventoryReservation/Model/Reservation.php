<?php
namespace Magenest\CustomInventoryReservation\Model;

/**
 * Class Reservation
 * @package Magenest\CustomInventoryReservation\Model
 */
class Reservation extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init(\Magenest\CustomInventoryReservation\Model\ResourceModel\Reservation::class);
    }

}
