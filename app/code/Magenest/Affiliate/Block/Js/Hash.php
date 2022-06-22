<?php


namespace Magenest\Affiliate\Block\Js;

use Magenest\RewardPoints\Cookie\ReferralCode;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\Config\Source\Urltype;

/**
 * Class Hash
 * @package Magenest\Affiliate\Block\Js
 */
class Hash extends Template
{
    /**
     * @var Data
     */
    protected $_affiliateHelper;

    /**
     * Hash constructor.
     *
     * @param Context $context
     * @param Data $affiliateHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $affiliateHelper,
        array $data = []
    ) {
        $this->_affiliateHelper = $affiliateHelper;

        parent::__construct($context, $data);
    }

    /**
     * Get prefix
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->_affiliateHelper->getUrlPrefix();
    }

    /**
     * Get cookie name
     * @return string
     */
    public function getCookieName()
    {
        return Data::AFFILIATE_COOKIE_NAME;
    }

    /**
     * Get cookie name
     * @return string
     */
    public function getCookieNameRewardPoint()
    {
        return ReferralCode::COOKIE_NAME;
    }

    /**
     * @return bool
     */
    public function checkCookie()
    {
        $urlType = $this->_affiliateHelper->getUrlType();

        return Urltype::TYPE_HASH === $urlType;
    }

    /**
     * @return float|int
     */
    public function getExpire()
    {
        $expireDay = (int)$this->_affiliateHelper->getExpiredTime();

        return $expireDay * 24 * 3600;
    }

    /**
     * @return mixed
     */
    public function getConfigCustomAffiliate()
    {
        $customCssConfig = $this->_affiliateHelper->getCustomCss();

        return $customCssConfig;
    }
}
