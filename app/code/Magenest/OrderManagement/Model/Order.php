<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderManagement\Model;

use Magenest\Core\Helper\Data as CoreData;
use Magenest\OrderCancel\Model\OrderManagement;
use Magenest\OrderManagement\Helper\Authorization;
use Magenest\OrderManagement\Helper\Config;
use Magento\Backend\Model\Url;
use Magento\Email\Model\Template\SenderResolver;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Registry;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Payment\Helper\Data as PaymentData;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity as IdentityInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Order
 * @package Magenest\OrderManagement\Model
 */
class Order
{
    /** @const Order status code */
    const WAIT_SUPPLIER_CODE = 'wait_supplier';
    const SUPPLIER_CONFIRMED_CODE = 'supplier_confirmed';
    const SUPPLIER_REJECTED_CODE = 'supplier_rejected';
    const RECEIVED_GOODS_CODE = 'received_goods';
    const CONFIRMED_WAREHOUSE_SALES_CODE = 'confirmed';
    const CONFIRMED_PAID_CODE = 'confirm_paid';
    const CONFIRMED_COD_CODE = 'confirmed_cod';
    const NEED_WAREHOUSE_CONFIRM_CODE = 'need_warehouse_confirm';
    const NEED_CONFIRM_REIMBURSEMENT_CODE = 'need_confirm_reimbursement';
    const REIMBURSED_CODE = 'reimbursed';
    const ORDER_COMPLETE_SHIPMENT = 'complete_shipment';

    /** @const Order status label */
    const WAIT_SUPPLIER_LABEL = 'Wait Supplier';
    const SUPPLIER_CONFIRMED_LABEL = 'Supplier Confirmed Availability';
    const SUPPLIER_REJECTED_LABEL = 'Supplier Rejected';
    const RECEIVED_GOODS_LABEL = 'Received Goods';
    const CONFIRMED_LABEL = 'Confirmed';
    const CONFIRMED_PAID_LABEL = 'Confirmed Paid';
    const CONFIRMED_COD_LABEL = 'Confirmed COD';
    const NEED_WAREHOUSE_CONFIRM_LABEL = 'Need Warehouse Confirm Receipt of the Items';
    const NEED_CONFIRM_REIMBURSEMENT_LABEL = 'Need Confirm Reimbursement';
    const REIMBURSED_LABEL = 'Reimbursed';
    const ORDER_COMPLETE_SHIPMENT_LABEL = 'Completed';

    /** @const Staff code manage orders */
    const CUSTOMER_SERVICE_STAFF = 'customer_service';
    const ACCOUNTING_STAFF = 'accounting';
    const SUPPLIER_STAFF = 'supplier';
    const WAREHOUSE_STAFF = 'warehouse';

    /** @const Config path */
    const SALES_EMAIL_ORDER_ENABLED_PATH = 'sales_email/order/enabled';
    const SALES_EMAIL_ORDER_IDENTITY_PATH = 'sales_email/order/identity';

    const ACCOUNTANT_ENABLED_PATH = 'email_communication/accountant/enabled';
    const ACCOUNTANT_IDENTITY_PATH = 'email_communication/accountant/identity';

    const CUSTOMER_SERVICE_ENABLED_PATH = 'email_communication/customer_service/enabled';
    const CUSTOMER_SERVICE_IDENTITY_PATH = 'email_communication/customer_service/identity';

    const SUPPLIER_ENABLE_PATH = 'email_communication/supplier/enabled';
    const SUPPLIER_IDENTITY_PATH = 'email_communication/supplier/identity';

    const WAREHOUSE_ENABLE_PATH = 'email_communication/warehouse/enabled';
    const WAREHOUSE_NOTIFICATION_PATH = 'email_communication/warehouse/warehouse_email_list';
    const WAREHOUSE_IDENTITY_PATH = 'email_communication/warehouse/identity';

    const ORDER_ITEM_IS_BACKORDER = 'is_backorder';

    /**
     * @var  SenderResolver
     */
    protected $_senderResolver;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var TemplateFactory
     */
    protected $_mailTemplateFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CoreData
     */
    protected $_coreData;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var IdentityInterface
     */
    protected $_identityContainer;

    /**
     * @var PaymentData
     */
    protected $_paymentData;

    /**
     * @var Renderer
     */
    protected $_addressRenderer;

    /**
     * @var Authorization
     */
    protected $_authorization;

    /** @var Url */
    protected $urlHelper;

    /** @var Registry */
    protected $registry;

    /**
     * Constructor.
     *
     * @param SenderResolver $senderResolver
     * @param TransportBuilder $transportBuilder
     * @param TemplateFactory $templateFactory
     * @param StoreManagerInterface $storeManager
     * @param CoreData $coreData
     * @param Config $config
     * @param PaymentData $paymentData
     * @param IdentityInterface $identityContainer
     * @param Renderer $renderer
     * @param Authorization $authorization
     * @param Url $urlHelper
     * @param Registry $registry
     */
    public function __construct(
        SenderResolver $senderResolver,
        TransportBuilder $transportBuilder,
        TemplateFactory $templateFactory,
        StoreManagerInterface $storeManager,
        CoreData $coreData,
        Config $config,
        PaymentData $paymentData,
        IdentityInterface $identityContainer,
        Renderer $renderer,
        Authorization $authorization,
        Url $urlHelper,
        Registry $registry
    ) {
        $this->_addressRenderer     = $renderer;
        $this->_identityContainer   = $identityContainer;
        $this->_paymentData         = $paymentData;
        $this->_config              = $config;
        $this->_senderResolver      = $senderResolver;
        $this->_transportBuilder    = $transportBuilder;
        $this->_mailTemplateFactory = $templateFactory;
        $this->_storeManager        = $storeManager;
        $this->_coreData            = $coreData;
        $this->_authorization       = $authorization;
        $this->urlHelper            = $urlHelper;
        $this->registry             = $registry;
    }

    /**
     * Can confirm order
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function canConfirm(OrderInterface $order)
    {
        return (in_array($order->getStatus(), ['pending', Order::RECEIVED_GOODS_CODE]));
    }

    /**
     * Can confirm paid
     *
     * @param OrderInterface $order
     * @param bool $isAction
     * @return bool
     */
    public function canConfirmPaid(OrderInterface $order, $isAction = true)
    {
        if (self::CONFIRMED_WAREHOUSE_SALES_CODE != $order->getStatus()) {
            return false;
        }

//        /** Order is instalment pending */
//        if (ConfigProvider::INSTALMENT_CODE == $order->getPayment()->getMethod()) {
//            return true;
//        }
        if ($isAction) {
            $invoiceCol = $order->getInvoiceCollection();

            return count($invoiceCol->getItems()) > 0;
        }

        return true;
    }

    /**
     * Can confirm paid
     *
     * @param OrderInterface $order
     * @param bool $isAction
     * @return bool
     */
    public function canConfirmDebt(OrderInterface $order)
    {
        if (self::CONFIRMED_WAREHOUSE_SALES_CODE != $order->getStatus()) {
            return false;
        }
        if ('checkmo' !== $order->getPayment()->getMethod()) {
            return false;
        }
        if ($order->getIsConfirmDebt()) {
            return false;
        }
//        /** Order is instalment pending */
//        if (ConfigProvider::INSTALMENT_CODE == $order->getPayment()->getMethod()) {
//            return true;
//        }
        return true;
    }

    /**
     * Wait online invoice
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function waitOnlineInvoice(OrderInterface $order)
    {
        return ($order->getConfirmPaidAt() && $order->canInvoice());
    }

    /**
     * Is COD order
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function isCODOrder(OrderInterface $order)
    {
        return $order->getPayment() && ($order->getPayment()->getMethod() == Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE);
    }

    /**
     * Send confirm email
     *
     * @param OrderInterface $order
     * @throws \Exception
     */
    public function sendEmailToCustomerWhenOrderConfirmed(OrderInterface $order)
    {
        if (!$this->_config->getStoreConfiguration(self::SALES_EMAIL_ORDER_ENABLED_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getCustomerConfirmPaidEmailTemplate($order->getStoreId()),
            self::SALES_EMAIL_ORDER_IDENTITY_PATH,
            $order->getCustomerEmail(),
            [],
            false
        );
    }

    /**
     * Send confirm email
     *
     * @param OrderInterface $order
     * @param bool $force
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function sendAccountantNotificationEmail($order, $force = false)
    {
        if (!$this->_config->getStoreConfiguration(self::ACCOUNTANT_ENABLED_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getCustomerConfirmCODEmailTemplate($order->getStoreId()),
            self::ACCOUNTANT_IDENTITY_PATH,
            $this->_config->getEmailSendToList('email_communication/accountant/list_email_customer_service_confirm'),
            [],
            true,
            $force
        );
    }

    /**
     * Send customer service notification email
     *
     * @param \Magento\Sales\Model\Order $order
     * @throws \Exception
     */
    public function sendCustomerServiceNotificationEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::CUSTOMER_SERVICE_ENABLED_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getCustomerServiceNotificationEmailTemplate($order->getStoreId()),
            self::CUSTOMER_SERVICE_IDENTITY_PATH,
            $this->_config->getEmailSendToList('email_communication/customer_service/list_email_notifier')
        );
    }

    /**
     * Send customer service notification email
     *
     * @param OrderInterface $order
     * @throws \Exception
     */
    public function sendCustomerServiceSupplierRejectNotificationEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::CUSTOMER_SERVICE_ENABLED_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getSupplierRejectEmailTemplate($order->getStoreId()),
            self::CUSTOMER_SERVICE_IDENTITY_PATH,
            $this->_config->getEmailSendToList('email_communication/customer_service/list_email_supplier_reject_delivery')
        );
    }

    /**
     * Send warehouse notification email
     *
     * @param \Magento\Sales\Model\Order $order
     * @throws \Exception
     */
    public function sendWarehouseNotificationEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::WAREHOUSE_ENABLE_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getWarehouseNotificationEmailTemplate($order->getStoreId()),
            self::WAREHOUSE_IDENTITY_PATH,
            $this->_config->getEmailSendToList('email_communication/customer_service/list_email_notifier_warehouse')
        );
    }

    /**
     * Send complete shipment email
     *
     * @param OrderInterface $order
     * @throws \Exception
     */
    public function sendCompleteShipmentEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::WAREHOUSE_ENABLE_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getCompleteShipmentEmailTemplate($order->getStoreId()),
            self::WAREHOUSE_IDENTITY_PATH,
            $order->getCustomerEmail(),
            [],
            false
        );
    }

    /**
     * Send warehouse returned order email
     *
     * @param OrderInterface $order
     * @throws \Exception
     */
    public function sendWarehouseReturnedOrderNotificationEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::WAREHOUSE_ENABLE_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getReturnedOrderNotificationEmailTemplate($order->getStoreId()),
            self::WAREHOUSE_IDENTITY_PATH,
            $this->_config->getEmailSendToList('email_communication/warehouse/list_email_new_returned_order')
        );
    }

    /**
     * Send supplier received goods
     *
     * @param OrderInterface $order
     * @throws \Exception
     */
    public function sendSupplierReceivedGoodsEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::SUPPLIER_ENABLE_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getSupplierReceivedGoodsEmailTemplate($order->getStoreId()),
            self::SUPPLIER_IDENTITY_PATH,
            $this->_config->getEmailSendToList('email_communication/supplier/list_email_warehouse_received_goods')
        );
    }

    /**
     * Send customer service received goods notification
     *
     * @param OrderInterface $order
     * @throws \Exception
     */
    public function sendCustomerServiceReceivedGoodsNotificationEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::CUSTOMER_SERVICE_ENABLED_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getCustomerServiceWarehouseEmailTemplate($order->getStoreId()),
            self::CUSTOMER_SERVICE_IDENTITY_PATH,
            $this->_config->getEmailSendToList('email_communication/customer_service/list_email_warehouse_received_goods')
        );
    }

    /**
     * Send canceled Email
     *
     * @param OrderInterface $order
     * @throws \Exception
     */
    public function sendCanceledEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::CUSTOMER_SERVICE_ENABLED_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            'om_canceled_email_template',
            self::CUSTOMER_SERVICE_IDENTITY_PATH,
            $order->getCustomerEmail(),
            [],
            false
        );
    }

    /**
     * Email sent to warehouse when accountant confirm payment
     *
     * @param OrderInterface $order
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendWarehouseNeedPackagingNotificationEmail($order)
    {
        if (!$this->_config->getStoreConfiguration(self::WAREHOUSE_ENABLE_PATH)) {
            return;
        }

        $this->sendMarketingEmail(
            $order,
            $this->_config->getWarehouseNeedPackagingEmailTemplate($order->getStoreId()),
            self::WAREHOUSE_IDENTITY_PATH,
            $this->_config->getEmailSendToList('email_communication/warehouse/list_email_need_packaging')
        );
    }

    /**
     * Send Marketing Email
     *
     * @param OrderInterface $order
     * @param string $templateId
     * @param string $from
     * @param string $to
     * @param array $cc
     * @param bool $sendInternal
     * @param bool $force
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function sendMarketingEmail(
        OrderInterface $order,
        $templateId,
        $from,
        $to = null,
        array $cc = [],
        $sendInternal = true,
        $force = false
    ) {
        try {
            if ($order->getIsPosOrder() && !$force) {
                return;
            }
            if (!$to || empty($to)) {
                return;
            }
            if (is_array($to)) {
                foreach ($to as &$mailTo) {
                    $mailTo = trim($mailTo);
                }
            }

            $storeId = $order->getStoreId();
            /** @var array $from */
            $from     = $this->_coreData->getConfigValue($from, $storeId);
            $to       = $this->_resoleEmailAddress($to, $storeId);
            $orderUrl = $orderNote = $reasonCancel = '';
            if ($sendInternal) {
                $orderUrl  = $this->urlHelper->getUrl('sales/order/view/', ['order_id' => $order->getEntityId()]);
                $orderNote = $this->getOrderNote($order);
            }

            if ($templateId === 'om_canceled_email_template') {
                $reasonCancel = $this->registry->registry(OrderManagement::REASON_CANCEL_ORDER_VAR_TEMPLATE);
            }

            $paymentMethod = $order->getPayment()->getAdditionalInformation()['method_title'];
            $templateVars = [
                'order' => $order,
                'billing' => $order->getBillingAddress(),
                'payment_method' => $paymentMethod,
                'payment_html' => $this->_getPaymentHtml($order),
                'store' => $order->getStore(),
                'formattedShippingAddress' => $this->_getFormattedShippingAddress($order),
                'formattedBillingAddress' => $this->_getFormattedBillingAddress($order),
                'order_url' => $orderUrl,
                'order_id' => $order->getEntityId(),
                'order_note' => $orderNote,
                'reason_cancel' => $reasonCancel,
                'created_at_formatted' => $order->getCreatedAtFormatted(2),
                'order_data' => [
                    'customer_name' => $order->getCustomerName(),
                    'is_not_virtual' => $order->getIsNotVirtual(),
                    'email_customer_note' => $order->getEmailCustomerNote(),
                    'frontend_status_label' => $order->getFrontendStatusLabel()
                ]
            ];

            $this->_transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to);

            foreach ($cc as $address) {
                $address = $this->_resoleEmailAddress($address, $storeId);
                $this->_transportBuilder->addCc($address);
            }

            $this->_transportBuilder->getTransport()->sendMessage();
        } catch (MailException $e) {
        }
    }

    /**
     * Get staffs
     *
     * @return array
     */
    public function getStaffLists()
    {
        return [
            self::CUSTOMER_SERVICE_STAFF,
            self::ACCOUNTING_STAFF,
            self::SUPPLIER_STAFF,
            self::WAREHOUSE_STAFF
        ];
    }

    /**
     * Resole email address
     *
     * @param string $email
     * @param int $storeId
     * @return string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Zend_Validate_Exception
     */
    private function _resoleEmailAddress($email, $storeId)
    {
        if (is_array($email)) {
            return $email;
        }

        if (!\Zend_Validate::is(trim($email), 'EmailAddress')) {
            $email = $this->_senderResolver->resolve($email, $storeId)['email'];
        }

        return $email;
    }

    /**
     * Returns payment info block as HTML.
     *
     * @param OrderInterface|SalesOrder $order
     * @return string
     * @throws \Exception
     */
    private function _getPaymentHtml($order)
    {
        return $this->_paymentData->getInfoBlockHtml($order->getPayment(), $this->_identityContainer->getStore()->getStoreId());
    }

    /**
     * Render shipping address into html.
     *
     * @param OrderInterface|SalesOrder $order
     * @return string|null
     */
    private function _getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual() ? null : $this->_addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Render billing address into html.
     *
     * @param $order
     * @return string|null
     */
    private function _getFormattedBillingAddress($order)
    {
        return $this->_addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * Get Order Note
     *
     * @param $order
     * @return string|null
     */
    private function getOrderNote($order)
    {
        $html = '';
        foreach ($order->getAllStatusHistory() as $orderComment) {
            $html .= "<p>" . $orderComment->getComment() . "</p>";
        }
        return $html;
    }
}
