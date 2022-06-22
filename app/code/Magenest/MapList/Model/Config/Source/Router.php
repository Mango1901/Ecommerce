<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ShopByBrand extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ShopByBrand
 * @author   Chienbigstar <chienbigstar@gmail.com>
 */
namespace Magenest\MapList\Model\Config\Source;

/**
 * Class Router
 *
 * @package Magenest\MapList\Model\Config\Source
 */
class Router
{

    const ROUTER_BRAND = 'brand';
    const ROUTER_MEDIA = 'maplist/brand/image';
    const ROUTER_STATIC = 'frontend/Magento/luma/en_US/Magento_Catalog/images/product/placeholder/thumbnail.jpg';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scope;

    /**
     * Router constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scope=$scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getRouterBrand()
    {
        return $this->scope->getValue('maplist/page/url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
