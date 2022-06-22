<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;

class Request extends Action
{
    protected $_helperData;
    protected $_orderFactory;

    public function __construct(
        \Magenest\ViettelPost\Helper\Data $helperData,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        Action\Context $context
    )
    {
        parent::__construct($context);
        $this->_orderFactory = $orderFactory;
        $this->_helperData = $helperData;
    }

    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        return parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create('json');
        $orderId = $this->getRequest()->getParam('order_id');
        $carrier = $this->getRequest()->getParam('carrier');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $carrierModel = $this->_helperData->getCarrierModel($carrier);
        if($carrier && $orderId && $carrierModel && $shipmentId){
            $order = $this->_orderFactory->create()->load($orderId);
            $shipment = $this->_objectManager
                             ->create(\Magento\Sales\Model\Order\Shipment::class)
                             ->load($shipmentId);
            try{
                $carrierModel->createShipment($order, $shipment);
                $shipment->setData(\Magenest\ViettelPost\Setup\UpgradeData::SHIPMENT_CARRIER_NAME, $carrierModel::CARRIER_NAME);
                $shipment->save();
            }catch (\Exception $e){
                return $resultJson->setData([
                    'error'=>true,
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }else{
            return $resultJson->setData([
                'error'=>true,
                'success' => false,
                'message' => 'Please select a shipping carrier'
            ]);
        }
        return $resultJson->setData([
            'error'=>false,
            'success' => true
        ]);
    }
}
