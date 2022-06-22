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

namespace Magenest\OrderManagement\Plugin\Magento\Sales\Model\Order\Email\Container;

use Magenest\OrderManagement\Model\Order;

class CreditmemoIdentity
{
    protected $helper;

    /**
     * CreditmemoIdentity constructor.
     *
     * @param \Magenest\OrderManagement\Helper\Config $helper
     */
    public function __construct(
        \Magenest\OrderManagement\Helper\Config $helper
    ) {
        $this->helper = $helper;
    }

    public function afterGetEmailCopyTo(\Magento\Sales\Model\Order\Email\Container\CreditmemoIdentity $creditmemoIdentity, $result)
    {
        if ($result && is_array($result)) {
            $result = array_merge($result, $this->helper->getEmailSendToList(Order::WAREHOUSE_NOTIFICATION_PATH));
        }

        return $result;
    }
}
