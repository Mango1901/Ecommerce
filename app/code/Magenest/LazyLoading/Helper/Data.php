<?php

namespace Magenest\LazyLoading\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        parent::__construct($context);
        $this->_layout = $layout;
    }

    /**
     * @return boolean
     */
    public function isEnabled() {
        return $this->scopeConfig->getValue('magenest_lazy_loading/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return boolean
     */
    public function isLazyAllImageEnable(){
        return $this->scopeConfig->getValue('magenest_lazy_loading/general/enable_all_image', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return boolean
     */
    public function isLazyIframeEnable(){
        return $this->scopeConfig->getValue('magenest_lazy_loading/general/enable_iframe', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getImageLoader() {
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }

    /**
     * @param string
     * @return string
     */
    public function lazyLoad($html)
    {
        $regex = '/<img([^>]*) src=+(?:"|\')([^"\']*)(?:"|\')([^>]*)>/mi';
        $replace = "<img lazy src=\"{$this->getImageLoader()}\" data-src=\"$2\" $1 $3>";
        $html = preg_replace($regex, $replace, $html);
        return $html;
    }

    /**
     * @param string
     * @return string
     */
    public function lazyLoadIframe($html)
    {
        $regex = '/<iframe ([^>]*)src[^"\']+(?:"|\')([^"\']*)(?:"|\')([^>]*)>([^<>]*)<\/iframe>/mi';
        $replace = "<iframe lazy src=\"\" data-original=\"$2\" $1 $3>$4</iframe>";
        $html = preg_replace($regex, $replace, $html);
        return $html;
    }

    /**
     * Checks if image src should be changed with lazyload preloader
     * @return boolean
     */
    public function isRequestAjax() {
        return $this->_request->isAjax() || $this->_verifyIfLazyLoadShouldBeIgnored();
    }

    /**
     * @return array
     */
    public function getIgnoreHandles()
    {
        return [
            'wishlist_email_items'
        ];
    }

    /**
     * @return bool
     */
    protected function _verifyIfLazyLoadShouldBeIgnored() {
        $layoutHandlesUsed = $this->_layout->getUpdate()->getHandles();
        $ignoredHandles = $this->getIgnoreHandles();
        if (count(array_intersect($layoutHandlesUsed, $ignoredHandles))) {
            return true;
        }

        return false;
    }
}