<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Observer;

use Magenest\ViettelPost\Model\ShippingCarrier;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ShipmentValidate
 * @package Magenest\ViettelPost\Observer
 */
class ShipmentValidate implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_requestInterface;

    protected $_orderFactory;

    protected $helper;

    /**
     * Constructor.
     *
     * @param RequestInterface $objectManager
     */
    function __construct(
        RequestInterface $requestInterface,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magenest\ViettelPost\Helper\Data $helper
    )
    {
        $this->_requestInterface = $requestInterface;
        $this->_orderFactory = $orderFactory;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $shipmentParams = $this->_requestInterface->getParam('shipment');
        $carrierName = isset($shipmentParams['carrier_select'])?$shipmentParams['carrier_select']:"";
        if($carrierName == ShippingCarrier::CARRIER_NAME){
            $vtProvinceId = $this->_requestInterface->getParam('province_id');
            $vtDistrictId = $this->_requestInterface->getParam('district_id');
            $vtWardsId = $this->_requestInterface->getParam('wards_id');
            if(!$vtProvinceId || !$vtDistrictId || !$vtWardsId){
                throw new \Exception("Please validate shipping address fields");
            }
        }
//        $shipment->setData(\Magenest\ViettelPost\Setup\UpgradeData::SHIPMENT_CARRIER_NAME, $carrierName);
//        return $this->helper->isShipmentValidate($order, $shipment);
    }
}
