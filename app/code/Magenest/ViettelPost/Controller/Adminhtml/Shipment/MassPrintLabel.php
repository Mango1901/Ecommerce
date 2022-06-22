<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Controller\Adminhtml\Shipment;

use Magenest\ViettelPost\Model\ShippingCarrier;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class MassPrintLabel extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction implements HttpPostActionInterface
{
    protected $helperData;
    protected $carrierModel;
    protected $viettelPostOrderCollectionFactory;

    public function __construct(
        \Magenest\ViettelPost\Helper\Data $helperData,
        \Magenest\ViettelPost\Model\ShippingCarrier $carrierModel,
        \Magenest\ViettelPost\Model\ResourceModel\ViettelOrder\CollectionFactory $viettelOrderCollectionFactory,
        Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter
    )
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->carrierModel = $carrierModel;
        $this->helperData = $helperData;
        $this->viettelPostOrderCollectionFactory = $viettelOrderCollectionFactory;
    }


    /*
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        //remove print func in order grid
        return ;
        $collection->addFieldToFilter("shipping_carrier", ShippingCarrier::CARRIER_NAME);
        $idList = $collection->getAllIds();
        $viettelOrderCol = $this->viettelPostOrderCollectionFactory->create()->addFieldToFilter('order_id', $idList);
        $viettelOrderNumber = $viettelOrderCol->getColumnValues('order_number');
        $resp = $this->carrierModel->requestShippingLabel($viettelOrderNumber);
        $respBody = isset($resp['body'])?$resp['body']:"";
        $respBody = json_decode($respBody, true);
        if(($respBody['status'] ?? 0) == 200) {
            $shippingLabelUrl = $respBody['message'] ?? "";
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($shippingLabelUrl);
            return $resultRedirect;
        }else{
            $message = $respBody['message'] ?? "";
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath("sales/order/view");
            $this->messageManager->addErrorMessage($message);
            return $resultRedirect;
        }
    }
}
