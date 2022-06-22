<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\OrderExtraInformation\Controller\Adminhtml\Order;

use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class ChangeShippingFee extends Order
{
    const ADMIN_RESOURCE = "Magenest_OrderExtraInformation::change_shipping";

    private $_orderUtility;

    /**
     * ChangeShippingFee constructor.
     *
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param \Magenest\OrderExtraInformation\Model\OrderUtility $orderUtility
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        \Magenest\OrderExtraInformation\Model\OrderUtility $orderUtility
    ) {
        $this->_orderUtility = $orderUtility;
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $order = $this->_initOrder();
        if ($order) {
            try {
                $amount = $this->getRequest()->getParam('shipping_fee', $order->getBaseShippingAmount());
                $originalAmount = $order->getShippingFee();
                $originalAmount = $this->_orderUtility->formatPrice($originalAmount);
                $order->setShippingFee($amount);
//                $this->_orderUtility->updateShippingFee($order, $amount);
                $this->orderRepository->save($order);
                if ($amount > 0) {
                    $comment = __("Shipping fee is increased with amount %1 from %2.", $this->_orderUtility->formatPrice($amount), $originalAmount);
                } else {
                    $comment = __("Shipping fee is decreased with amount %1 from %2.", $this->_orderUtility->formatPrice(-$amount), $originalAmount);
                }

                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => $comment
                ]);
                $this->messageManager->addSuccessMessage(__('You updated the shipping fee for this order.'));

                return $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong, please try again later.'));
            }
        }

        return $resultRedirect->setPath('sales/*/');
    }
}
