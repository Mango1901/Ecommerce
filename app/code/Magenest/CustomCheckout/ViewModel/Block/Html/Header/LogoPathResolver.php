<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 29/11/2021
 * Time: 16:56
 */

namespace Magenest\CustomCheckout\ViewModel\Block\Html\Header;

use Magento\Config\Model\Config\Backend\Image\Logo;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Theme\ViewModel\Block\Html\Header\LogoPathResolverInterface;

class LogoPathResolver implements LogoPathResolverInterface, ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getPath(): ?string
    {
        $path = null;
        $storeLogoPath = $this->scopeConfig->getValue(
            'design/header/checkout_logo_src',
            ScopeInterface::SCOPE_STORE
        );
        if ($storeLogoPath !== null) {
            $path = Logo::UPLOAD_DIR . '/' . $storeLogoPath;
        }
        return $path;
    }
}
