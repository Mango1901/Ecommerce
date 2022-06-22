<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Helper;

class DefaultConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TXT_COLOR_XML_PATH = 'mega_menu/default_conf/txt_color';
    const HOV_TXT_COLOR_XML_PATH = 'mega_menu/default_conf/hov_txt_color';
    const HOV_ICON_COLOR_XML_PATH = 'mega_menu/default_conf/hov_icon_color';
    const BTN_BG_COLOR_XML_PATH = 'mega_menu/default_conf/btn_bg_color';
    const HOV_BTN_BG_COLOR_XML_PATH = 'mega_menu/default_conf/hov_btn_bg_color';
    const DRD_BG_COLOR_XML_PATH = 'mega_menu/default_conf/drd_bg_color';
    const DRD_BG_OPACITY_XML_PATH = 'mega_menu/default_conf/drd_bg_opacity';
    const DISABLE_OWL_CAROUSEL_XML_PATH = 'mega_menu/general/disable_owl_carousel';
    
    public function getValuesAsArr()
    {
        return [
            'textColor' => $this->getTextColor(),
            'hoverTextColor' => $this->getHoverTextColor(),
            'hoverIconColor' => $this->getHoverIconColor(),
            'buttonBackgroundColor' => $this->getButtonBackgroundColor(),
            'color' => $this->getHoverButtonBackgroundColor(),
            'backgroundColor' => $this->getDropdownBackgroundColor(),
            'backgroundOpacity' => $this->getDropdownBackgroundOpacity()
        ];
    }

    public function getTextColor()
    {
        return $this->scopeConfig->getValue(self::TXT_COLOR_XML_PATH);
    }

    public function getHoverTextColor()
    {
        return $this->scopeConfig->getValue(self::HOV_TXT_COLOR_XML_PATH);
    }

    public function getHoverIconColor()
    {
        return $this->scopeConfig->getValue(self::HOV_ICON_COLOR_XML_PATH);
    }

    public function getButtonBackgroundColor()
    {
        return $this->scopeConfig->getValue(self::BTN_BG_COLOR_XML_PATH);
    }

    public function getHoverButtonBackgroundColor()
    {
        return $this->scopeConfig->getValue(self::HOV_BTN_BG_COLOR_XML_PATH);
    }

    public function getDropdownBackgroundColor()
    {
        return $this->scopeConfig->getValue(self::DRD_BG_COLOR_XML_PATH);
    }

    public function getDropdownBackgroundOpacity()
    {
        return $this->scopeConfig->getValue(self::DRD_BG_OPACITY_XML_PATH);
    }

    public function getDisableOwlCarousel($storeId = null, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(self::DISABLE_OWL_CAROUSEL_XML_PATH, $scope, $storeId);
    }
}
