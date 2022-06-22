<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class Config
{
    /**
     * Configuration path to Advanced Reports order statuses
     */
    const XML_PATH_GENERAL_ORDER_STATUS = 'aw_advancedreports/general/order_status';

    /**
     * Configuration path to locale weekend
     */
    const XML_PATH_GENERAL_LOCALE_WEEKEND = 'general/locale/weekend';

    /**
     * Configuration path to locale firstday
     */
    const XML_PATH_GENERAL_LOCALE_FIRSTDAY = 'general/locale/firstday';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get order status
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ORDER_STATUS);
    }

    /**
     * Get locale weekend
     *
     * @return string
     */
    public function getLocaleWeekend()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_LOCALE_WEEKEND);
    }

    /**
     * Get locale firstday
     *
     * @return string
     */
    public function getLocaleFirstday()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_LOCALE_FIRSTDAY);
    }
}
