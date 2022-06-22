<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface GeneralConfigInterface
 * @api
 */
interface GeneralConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENABLED = 'enabled';

    const ENABLE_BANNER = 'enable_banner';

    const EXPIRED_TIME = 'expired_time';

    const OVERWRITE_COOKIES = 'overwrite_cookies';

    const USE_CODE_AS_COUPON = 'use_code_as_coupon';

    const SHOW_LINK = 'show_link';

    const PAGE_WELCOME = 'page_welcome';

    const URL_TYPE = 'url_type';

    const URL_PREFIX = 'url_prefix';

    const URL_PARAM = 'url_param';

    const URL_CODE_LENGTH = 'url_code_length';

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnabled($value);

    /**
     * @return bool
     */
    public function getEnableBanner();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnableBanner($value);

    /**
     * @return int
     */
    public function getExpiredTime();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setExpiredTime($value);

    /**
     * @return bool
     */
    public function getOverwriteCookies();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setOverwriteCookies($value);

    /**
     * @return bool
     */
    public function getUseCodeAsCoupon();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setUseCodeAsCoupon($value);

    /**
     * @return string
     */
    public function getShowLink();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setShowLink($value);

    /**
     * @return string
     */
    public function getPageWelcome();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPageWelcome($value);

    /**
     * @return string
     */
    public function getUrlType();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrlType($value);

    /**
     * @return string
     */
    public function getUrlPrefix();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrlPrefix($value);

    /**
     * @return string
     */
    public function getUrlParam();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrlParam($value);

    /**
     * @return int
     */
    public function getUrlCodeLength();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setUrlCodeLength($value);
}
