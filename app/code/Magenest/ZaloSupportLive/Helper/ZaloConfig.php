<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 18/12/2019
 * Time: 15:45
 */

namespace Magenest\ZaloSupportLive\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magenest\ZaloSupportLive\Model\Config;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ZaloConfig implements ArgumentInterface
{
    /** @var ScopeConfigInterface */
    private $config;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var int|null */
    private $websiteId = null;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getWebsiteId()
    {
        if ($this->websiteId === null) {
            $this->websiteId = $this->storeManager->getWebsite()->getId();
        }

        return $this->websiteId;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEnable()
    {
        return $this->config->getValue(Config::ZALO_ENABLE, ScopeInterface::SCOPE_WEBSITES, $this->getWebsiteId());
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOaId()
    {
        return $this->config->getValue(Config::ZALO_OAID, ScopeInterface::SCOPE_WEBSITES, $this->getWebsiteId());
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWelcomeMessage()
    {
        return $this->config->getValue(
            Config::ZALO_WELCOME_MESSAGE,
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAutoPopup()
    {
        return (int)$this->config->getValue(
            Config::ZALO_AUTO_POPUP,
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getZaloPopupWidth()
    {
        return (int)$this->config->getValue(
            Config::ZALO_POPUP_WIDTH,
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        );
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getZaloPopupHeight()
    {
        return (int)$this->config->getValue(
            Config::ZALO_POPUP_HEIGHT,
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        );
    }

    /**
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDisplayTimeout()
    {
        return (int)$this->config->getValue(
            Config::ZALO_DISPLAY_TIMEOUT,
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        ) * 1000;
    }
}
