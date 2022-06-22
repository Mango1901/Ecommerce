<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ShipmentCreate
 * @package Magenest\ViettelPost\Observer
 */
class ShipmentCreate implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_requestInterface;

    protected $_orderFactory;

    protected $helper;

    protected $messageManager;

    /**
     * Constructor.
     *
     * @param RequestInterface $objectManager
     */
    function __construct(
        RequestInterface $requestInterface,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magenest\ViettelPost\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_requestInterface = $requestInterface;
        $this->_orderFactory = $orderFactory;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $shipmentParams = $this->_requestInterface->getParam('shipment');
        $carrierName = isset($shipmentParams['carrier_select'])?$shipmentParams['carrier_select']:"";

        if($this->helper->isShipmentShouldCreate($order, $shipment)){
            //Validate address for vietel post shipment
            if($carrierName == \Magenest\ViettelPost\Model\ShippingCarrier::CARRIER_NAME){
                $vtProvinceId = $this->_requestInterface->getParam('province_id');
                $vtDistrictId = $this->_requestInterface->getParam('district_id');
                $vtWardsId = $this->_requestInterface->getParam('wards_id');
                if(!$vtProvinceId || !$vtDistrictId || !$vtWardsId){
                    throw new LocalizedException(__("Please validate ViettelPost shipping address fields"));
                }
            }
            $carrierModel = $this->helper->getCarrierModel($carrierName);
            if($carrierModel){
                try{
                    $carrierModel->createShipment($order, $shipment);
                }catch (\Exception $e){
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                $shipment->setData(\Magenest\ViettelPost\Setup\UpgradeData::SHIPMENT_CARRIER_NAME, $carrierModel::CARRIER_NAME);
            }
        }
        return $this;
    }
}
