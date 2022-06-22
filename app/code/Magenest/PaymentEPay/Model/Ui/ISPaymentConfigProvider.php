<?php

namespace Magenest\PaymentEPay\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use  Magento\Payment\Gateway\ConfigInterface;

/**
 * Class DomesticConfigProvider
 * @package Magenest\OnePay\Model\Ui
 */
class ISPaymentConfigProvider implements ConfigProviderInterface
{
    const CODE = 'vnpt_epay_is';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * DomesticConfigProvider constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterFace $config
    ) {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                $this::CODE => [
                    'isActive' => $this->config->getValue('active')
                ]
            ]
        ];
    }
}
