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

use Magento\Quote\Model\Quote;
use Magento\Sales\Controller\Adminhtml\Order;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class InfoSave extends Order
{
    const ADMIN_RESOURCE = "Magenest_OrderExtraInformation::update_additional_info";

    protected $quote;

    protected $quoteResource;

    /**
     * InfoSave constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
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
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
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
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResource
    ) {
        $this->quote = $quoteFactory;
        $this->quoteResource = $quoteResource;
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $orderParams = $this->getRequest()->getParam('order');
                $order->setCustomerNote($orderParams['customer_note']);
//                $order->setDeliveryDate($orderParams['delivery_date']);
                $order->setDeliveryTime($orderParams['delivery_time']);
                $isWholesale = isset($orderParams['is_wholesale_order']) && !empty($orderParams['is_wholesale_order']);
                $order->setIsWholesaleOrder($isWholesale);

                $order->setCompanyName(null);
                $order->setTaxCode(null);
                $order->setCompanyAddress(null);
                $order->setCompanyEmail(null);
                if (isset($orderParams['save_vat_invoice'])) {
                    $order->setCompanyName($orderParams['company_name']);
                    $order->setTaxCode($orderParams['tax_code']);
                    $order->setCompanyAddress($orderParams['company_address']);
                    $order->setCompanyEmail($orderParams['company_email']);
                    $order->setTelephoneCustomerConsign($orderParams['telephone_customer_consign']);
                }
                $this->updateQuoteInformation($order->getQuoteId(), $orderParams);

                $this->orderRepository->save($order);

                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Additional Information changed.")
                ]);

                $this->messageManager->addSuccessMessage(__('You updated the order\'s additional information.'));
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong, please try again later.'));
            }
        }

        return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
    }

    private function updateQuoteInformation($quoteId, $orderParams)
    {
        /** @var Quote $quote */
        $quote = $this->quote->create();
        $this->quoteResource->load($quote, $quoteId);
        if (!$quote->getId()) {
            return;
        }
        $quote->setCustomerNote($orderParams['customer_note']);
//        $quote->setDeliveryDate($orderParams['delivery_date']);
        $quote->setDeliveryTime($orderParams['delivery_time']);
        $isWholesale = isset($orderParams['is_wholesale_order']) && !empty($orderParams['is_wholesale_order']);
        $quote->setIsWholesaleOrder($isWholesale);
        $quote->setCompanyName(null);
        $quote->setTaxCode(null);
        $quote->setCompanyAddress(null);
        $quote->setCompanyEmail(null);
        if ($orderParams['save_vat_invoice'] ?? false) {
            $quote->setCompanyName($orderParams['company_name']);
            $quote->setTaxCode($orderParams['tax_code']);
            $quote->setCompanyAddress($orderParams['company_address']);
            $quote->setCompanyEmail($orderParams['company_email']);
        }

        $this->quoteResource->save($quote);
    }
}
