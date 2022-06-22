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

namespace Magenest\OrderManagement\Plugin\Magento\Sales\Model\Order;

class Config
{
    private $_migrateStatus = [
        "closed_failed",
        "closed_ok",
        "failed_delivery",
        "finance_pending",
        "finance_verified",
        "picklisting",
        "re_delivery",
    ];

    /**
     * Remove unused status for status options
     *
     * @param \Magento\Sales\Model\Order\Config $config
     * @param $result
     * @return array
     */
    public function afterGetStateStatuses(\Magento\Sales\Model\Order\Config $config, $result)
    {
        if (is_array($result)) {
            foreach ($result as $status => $label) {
                if (in_array($status, $this->_migrateStatus)) {
                    unset($result[$status]);
                }
            }
        }

        return $result;
    }
}
