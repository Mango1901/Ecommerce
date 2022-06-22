<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Psr\Log\LoggerInterface;
use Magenest\OrderManagement\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Controller\Adminhtml\Order as Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;


/**
 * Class ConfirmPaid
 *
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class ConfirmPaid extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::accounting_confirm';

    /**
     * @var Order
     */
    protected $_omOrder;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param InlineInterface $translateInline
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param RawFactory $resultRawFactory
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param Order $omOrder
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        InlineInterface $translateInline,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $resultLayoutFactory,
        RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        Order $omOrder
    )
    {
        $this->_omOrder = $omOrder;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($order->getStatus() != Order::CONFIRMED_WAREHOUSE_SALES_CODE) {
                    return $this->resultRedirectFactory->create()->setPath('sales/*/');
                }

                if (!$this->_omOrder->canConfirmPaid($order)) {
                    $this->messageManager->addNoticeMessage(__('Order has no invoice, please create invoice before you confirm this order was paid.'));
                    return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
                }

                if ($this->_omOrder->isCODOrder($order)) {
                    $status = Order::CONFIRMED_COD_CODE;
                    $tag = 'COD';
                } else {
                    $status = Order::CONFIRMED_PAID_CODE;
                    $tag = 'fully paid';
                }

                $order->setState('processing')->setStatus($status)->setConfirmPaidAt((new \DateTime())->format('Y-m-d H:i:s'));
                $this->orderRepository->save($order);

                $this->messageManager->addSuccessMessage(__('You have confirmed this order paid by customer.'));
                $this->_omOrder->sendWarehouseNeedPackagingNotificationEmail($order);
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Accountant confirm order is %1.", $tag)
                ]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
