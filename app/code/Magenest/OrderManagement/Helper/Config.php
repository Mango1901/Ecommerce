<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 * @package Magenest\OrderManagement\Helper
 */
class Config extends AbstractHelper
{
    /** @const - Config email template */
    const CUSTOMER_SERVICE_CONFIRM_PAID_EMAIL_TEMPLATE = 'email_communication/customer_service/confirm_paid_template';
    const CUSTOMER_SERVICE_CUSTOMER_CONFIRM_PAID_EMAIL_TEMPLATE = 'email_communication/customer_service/customer_confirm_paid_template';
    const CUSTOMER_SERVICE_WAREHOUSE_RECEIVED_GOODS_EMAIL_TEMPLATE = 'email_communication/customer_service/warehouse_received_goods_email_template';
    const CUSTOMER_SERVICE_NOTIFICATION_EMAIL_TEMPLATE = 'email_communication/customer_service/notifier_email_template';
    const CUSTOMER_SERVICE_SUPPLIER_REJECT_EMAIL_TEMPLATE = 'email_communication/customer_service/supplier_reject_delivery_email_template';
    const CUSTOMER_SERVICE_CONFIRM_COD_EMAIL_TEMPLATE = 'email_communication/accountant/customer_service_confirm_email_template';
    const WAREHOUSE_NOTIFICATION_EMAIL_TEMPLATE = 'email_communication/customer_service/notifier_warehouse_email_template';
    const WAREHOUSE_RETURNED_ORDER_EMAIL_TEMPLATE = 'email_communication/warehouse/new_returned_order_email_template';
    const WAREHOUSE_COMPLETE_SHIPMENT_EMAIL_TEMPLATE = 'sales_email/order/complete_shipment_customer_template';
    const SUPPLIER_RECEIVED_GOODS_EMAIL_TEMPLATE = 'email_communication/supplier/warehouse_received_goods_email_template';

    /**
     * Get confirm paid email template
     *
     * @param null $storeId
     * @return mixed|string
     */
    public function getConfirmPaidEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration(self::CUSTOMER_SERVICE_CONFIRM_PAID_EMAIL_TEMPLATE, 'stores', $storeId);
        if (empty($template)) {
            $template = "email_communication_customer_service_confirm_paid_template";
        }

        return $template;
    }

    /**
     * Get customer confirm paid email template
     *
     * @param null $storeId
     * @return mixed|string
     */
    public function getCustomerConfirmPaidEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration(self::CUSTOMER_SERVICE_CUSTOMER_CONFIRM_PAID_EMAIL_TEMPLATE, 'stores', $storeId);
        if (empty($template)) {
            $template = "sales_email_order_confirm_paid_customer_template";
        }

        return $template;
    }

    /**
     * Get customer service notification email template
     *
     * @param null $storeId
     * @return mixed|string
     */
    public function getCustomerServiceNotificationEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration(self::CUSTOMER_SERVICE_NOTIFICATION_EMAIL_TEMPLATE, 'stores', $storeId);
        if (empty($template)) {
            $template = "email_communication_customer_service_notifier_email_template";
        }

        return $template;
    }

    public function getSupplierRejectEmailTemplate($storeId = null)
    {
        return $this->getStoreConfiguration(self::CUSTOMER_SERVICE_SUPPLIER_REJECT_EMAIL_TEMPLATE, 'stores', $storeId);
    }

    public function getCustomerConfirmCODEmailTemplate($storeId = null)
    {
        return $this->getStoreConfiguration(self::CUSTOMER_SERVICE_CONFIRM_COD_EMAIL_TEMPLATE, 'stores', $storeId);
    }

    /**
     * Get warehouse notification email template
     *
     * @param null $storeId
     * @return mixed|string
     */
    public function getWarehouseNotificationEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration(self::WAREHOUSE_NOTIFICATION_EMAIL_TEMPLATE, 'stores', $storeId);
        if (empty($template)) {
            $template = "email_communication_customer_service_notifier_warehouse_email_template";
        }

        return $template;
    }

    /**
     * Get returned notification email template
     *
     * @param null $storeId
     * @return mixed|string
     */
    public function getReturnedOrderNotificationEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration(self::WAREHOUSE_RETURNED_ORDER_EMAIL_TEMPLATE, 'stores', $storeId);
        if (empty($template)) {
            $template = "email_communication_warehouse_new_returned_order_email_template";
        }

        return $template;
    }

    public function getSupplierReceivedGoodsEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration(self::SUPPLIER_RECEIVED_GOODS_EMAIL_TEMPLATE, 'stores', $storeId);
        if (empty($template)) {
            $template = "email_communication_supplier_warehouse_received_goods_email_template";
        }

        return $template;
    }

    public function getCustomerServiceWarehouseEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration(self::CUSTOMER_SERVICE_WAREHOUSE_RECEIVED_GOODS_EMAIL_TEMPLATE, 'stores', $storeId);
        if (empty($template)) {
            $template = "email_communication_customer_service_warehouse_received_goods_email_template";
        }

        return $template;
    }

    public function getWarehouseNeedPackagingEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration('email_communication/warehouse/need_packaging_email_template', 'stores', $storeId);
        if (empty($template)) {
            $template = "email_communication_warehouse_need_packaging_email_template";
        }

        return $template;
    }

    /**
     * Get complete shipment email template
     *
     * @param null $storeId
     * @return mixed|string
     */
    public function getCompleteShipmentEmailTemplate($storeId = null)
    {
        $template = $this->getStoreConfiguration(self::WAREHOUSE_COMPLETE_SHIPMENT_EMAIL_TEMPLATE, 'stores', $storeId);
        if (empty($template)) {
            $template = "sales_email_order_complete_shipment_customer_template";
        }

        return $template;
    }

    /**
     * Get email send to list
     *
     * @param $configPath
     * @return array
     */
    public function getEmailSendToList($configPath)
    {
        $list = $this->getStoreConfiguration($configPath);
        if (isset($list) && !empty($list)) {
            return explode(',', $list);
        }

        return [];
    }

    /**
     * Get configuration value
     *
     * @param $path
     * @param string $scope
     * @param null $scopeId
     * @return mixed
     */
    public function getStoreConfiguration($path, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = null)
    {
        return $this->scopeConfig->getValue($path, $scope, $scopeId);
    }
}