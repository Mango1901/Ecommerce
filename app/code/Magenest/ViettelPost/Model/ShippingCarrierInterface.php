<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 07/11/2019
 * Time: 10:54
 */
namespace Magenest\ViettelPost\Model;

interface ShippingCarrierInterface{
    /**
     * @param $order
     * @return mixed
     * @throws \Exception
     */
    public function createShipment($order, $shipment);

    public function handleShippingStatusResponse($response);

    public function getShipmentData($shipmentId);
}