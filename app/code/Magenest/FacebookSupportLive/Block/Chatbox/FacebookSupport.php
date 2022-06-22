<?php
/**
 * Copyright (c) Magenest, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\FacebookSupportLive\Block\Chatbox;

use Magenest\FacebookSupportLive\Model\Config\Source\Type;
use Magento\Framework\View\Element\Template;
use \Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class FacebookSupport extends Template
{
    /** @var mixed|null */
    private $currentWebsiteId = null;

    /** @var Json */
    protected $serializer;

    /**
     * FacebookSupport constructor.
     *
     * @param Template\Context $context
     * @param Json $serializer
     * @param array $data
     */
    public function __construct(Template\Context $context, Json $serializer, array $data = [])
    {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
    }

    /**
     * @return int|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getWebsiteId()
    {
        if ($this->currentWebsiteId === null) {
            $this->currentWebsiteId = $this->_storeManager->getWebsite()->getId();
        }

        return $this->currentWebsiteId;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPageId()
    {
        return $this->_scopeConfig->getValue(
            'fb_support/general_setting/page_id',
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        );
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEnabledChatBox()
    {
        return $this->_scopeConfig->isSetFlag(
            'fb_support/general_setting/enable_fb',
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        );
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isUseCode()
    {
        return $this->getConfigType() == Type::USE_CODE;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isUseSettings()
    {
        return $this->getConfigType() == Type::USE_SETTINGS;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLocale()
    {
        return $this->_storeManager->getStore()->getCode() == "vn" ? "vi_VN" : "en_US";
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCode()
    {
        return $this->_scopeConfig->getValue(
            'fb_support/general_setting/code',
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        );
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfigType()
    {
        return $this->_scopeConfig->getValue(
            'fb_support/general_setting/config_type',
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        );
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDisplayTimeout()
    {
        return (int)$this->_scopeConfig->getValue(
            'fb_support/general_setting/display_timeout',
            ScopeInterface::SCOPE_WEBSITES,
            $this->getWebsiteId()
        ) * 1000;
    }
}
