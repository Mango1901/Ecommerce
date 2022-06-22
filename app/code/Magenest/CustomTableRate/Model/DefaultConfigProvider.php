<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Shipping\Model\Config as CustomTableRateConfig;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

/**
 * Class
 * @package Magenest\CustomTableRate\DefaultConfigProvider
 */
class DefaultConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CustomTableRateConfig
     */
    protected $shippingMethodConfig;

    /**
     * Constructor.
     *
     * @param CustomTableRateConfig $shippingConfig
     */
    public function __construct(
        CustomTableRateConfig $shippingConfig
    )
    {
        $this->shippingMethodConfig = $shippingConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'shipping' => [
                'carriers' => $this->_getActiveCarriers()
            ]
        ];
    }

    /**
     * Returns active carriers codes
     *
     * @return array
     */
    private function _getActiveCarriers()
    {
        $activeCarriers = [];
        /** @var AbstractCarrier $carrier */
        foreach ($this->shippingMethodConfig->getActiveCarriers() as $carrier) {
            $activeCarriers[$carrier->getCarrierCode()] = [
                'code' => $carrier->getCarrierCode(),
                'description' => $carrier->getConfigData('description') ?: ''
            ];
        }
        return $activeCarriers;
    }
}
