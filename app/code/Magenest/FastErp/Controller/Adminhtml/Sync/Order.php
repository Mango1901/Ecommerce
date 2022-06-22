<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 02/12/2021
 * Time: 17:11
 */

namespace Magenest\FastErp\Controller\Adminhtml\Sync;


use Magenest\FastErp\Model\UpdateOrder;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;

class Order extends Action
{
    public const ADMIN_RESOURCE = 'Magenest_FastErp::erp_sync_order';

    protected $orderRepository;

    protected $updateOrder;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        UpdateOrder $updateOrder,
        Context $context
    ) {
        $this->orderRepository = $orderRepository;
        $this->updateOrder = $updateOrder;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($id);

        try {
            $this->updateOrder->execute($order);
            $this->messageManager->addSuccessMessage(__('Order has been synced to ERP'));
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception);
        }

        return $this->_redirect('sales/order/view', ['order_id' => $id]);
    }
}
