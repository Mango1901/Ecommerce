<?php
/**
 * Router list
 * Used as a container for list of routers
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CouponCode\Model;

class Configurations
{
    /**
     * List of configuration
     *
     * @var ConfigurationInterface[]
     */
    protected $configurations;

    /**
     * @param array $configurations
     */
    public function __construct(array $configurations)
    {
        $this->configurations = array_filter(
            $configurations,
            function ($item) {
                return (!isset($item['disable']) || !$item['disable']) && $item['class'];
            }
        );
        uasort($this->configurations, [$this, 'compareRoutersSortOrder']);
    }

    /**
     * Compare configuration sortOrder
     *
     * @param array $configurationDataFirst
     * @param array $configurationDataSecond
     * @return int
     */
    protected function compareRoutersSortOrder($configurationDataFirst, $configurationDataSecond)
    {
        return (int)$configurationDataFirst['sortOrder'] <=> (int)$configurationDataSecond['sortOrder'];
    }

    /**
     * Get configuration
     *
     * @return array|ConfigurationInterface[]
     */
    public function getData()
    {
        return $this->configurations;
    }
}
