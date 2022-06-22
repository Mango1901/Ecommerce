<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 26/11/2021
 * Time: 14:20
 */

namespace Magenest\CustomCheckout\Model;


use Magento\CardinalCommerce\Model\Config;
use Magento\CardinalCommerce\Model\Request\TokenBuilder;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;

class AddAddressDefaultUrl implements ConfigProviderInterface
{
    /**
     * @var TokenBuilder
     */
    private $urlBuilder;


    /**
     * AddAddressDefaultUrl constructor.
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->urlBuilder = $url;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return [
            'defaultAddressUrl' => $this->urlBuilder->getUrl('customer/address/index')
        ];
    }
}
