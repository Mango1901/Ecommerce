<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;

class PrintLabel extends Action
{
    protected $_helperData;
    protected $_orderFactory;

    public function __construct(
        \Magenest\ViettelPost\Helper\Data $helperData,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->_orderFactory = $orderFactory;
        $this->_helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('id');
        $carrierModel = $this->_helperData->getCarrierModel('viettel_post');
        $shippingLabelUrl = $carrierModel->getShippingLabelUrl($shipmentId);

        if (!$shippingLabelUrl) {
            //get shippingLabel
            $orderNumber = $carrierModel->getOrderNumber($shipmentId);
            $resp = $carrierModel->requestShippingLabel($orderNumber);
            $respBody = isset($resp['body']) ? $resp['body'] : "";
            $respBody = json_decode($respBody, true);
            if (($respBody['status'] ?? 0) == 200) {
                $shippingLabelUrl = $respBody['message'] ?? "";
                //Save data
                $carrierDataObj = $carrierModel->getDataObj($shipmentId);
                $carrierDataObj->setData('shipping_label_url', $shippingLabelUrl);
                $carrierDataObj->save();
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($shippingLabelUrl);
                return $resultRedirect;
            } else {
                $message = $respBody['message'] ?? "";
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath("admin/order_shipment/view", ['shipment_id' => $shipmentId]);
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect;
            }
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($shippingLabelUrl);
            return $resultRedirect;
        }
        return $this->resultFactory->create('raw')->setContents('<script>window.open(\'\',\'_self\').close();</script>');
    }
}
