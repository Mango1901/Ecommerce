<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 07/11/2019
 * Time: 10:54
 */
namespace Magenest\ViettelPost\Model;

use Magenest\ViettelPost\Model\ShippingCarrierInterface;
use Magenest\ViettelPost\Helper\Data;
use Magento\Framework\App\RequestInterface;

class OfflineCarrier implements ShippingCarrierInterface
{
    const CARRIER_NAME = 'offline_carrier';

    public function handleShippingStatusResponse($response)
    {
        // TODO: Implement handleShippingStatusResponse() method.
    }

    public function getShipmentData($shipmentId)
    {
        // TODO: Implement getShipmentData() method.
    }

    /**
     * @param $order
     * @return mixed
     * @throws \Exception
     */
    public function createShipment($order, $shipment)
    {
        // TODO: Implement createShipment() method.
    }
}