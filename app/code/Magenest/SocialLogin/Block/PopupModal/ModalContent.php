<?php

namespace Magenest\SocialLogin\Block\PopupModal;

use Magento\Framework\View\Element\Template;

/**
 * Class ModalContent
 * @package Magenest\SocialLogin\Block\PopupModal
 */
class ModalContent extends Template
{
    /**
     * @var array|\Magento\Checkout\Block\Checkout\LayoutProcessorInterface[]
     */
    protected $layoutProcessors;

    /**
     * @var \Magenest\SocialLogin\Model\Facebook\Client
     */
    protected $_clientFacebook;

    /**
     * @var \Magenest\SocialLogin\Model\Google\Client
     */
    protected $_clientGoogle;

    /**
     * @var \Magenest\SocialLogin\Helper\SocialLogin
     */
    protected $_sociallogin;

    /**
     * @var \Magenest\SocialLogin\Model\Apple\Client
     */
    protected $_clientApple;

    /**
     * ModalContent constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\SocialLogin\Model\Facebook\Client      $clientFacebook
     * @param \Magenest\SocialLogin\Model\Google\Client        $clientGoogle
     * @param \Magenest\SocialLogin\Helper\SocialLogin         $socialLogin
     * @param array                                            $layoutProcessors
     * @param array                                            $data
     */
    public function __construct(
        Template\Context $context,
        \Magenest\SocialLogin\Model\Facebook\Client $clientFacebook,
        \Magenest\SocialLogin\Model\Google\Client $clientGoogle,
        \Magenest\SocialLogin\Model\Apple\Client $clientApple,
        \Magenest\SocialLogin\Helper\SocialLogin $socialLogin,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsLayout         = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->layoutProcessors = $layoutProcessors;
        $this->_clientFacebook  = $clientFacebook;
        $this->_clientGoogle    = $clientGoogle;
        $this->_clientApple     = $clientApple;
        $this->_sociallogin     = $socialLogin;
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'baseUrl'                  => $this->getBaseUrl(),
            'isButtonEnabledCheckout'  => $this->isButtonEnabledCheckout(),
            'isEnabledPopup'           => $this->isEnabledPopup(),
            'isEnabledInCreateAccount' => $this->isEnabledInCreateAccount(),
            'FacebookUrl'              => $this->getFacebookUrl(),
            'isFacebookEnabled'        => $this->isFacebookEnabled(),
            'GoogleUrl'                => $this->getGoogleUrl(),
            'isGoogleEnabled'          => $this->isGoogleEnabled(),
            'AppleUrl'                 => $this->getAppleUrl(),
            'isAppleEnabled'           => $this->isAppleEnabled(),
        ];
    }

    /**
     * @return string
     */
    public function getFacebookUrl()
    {
        return $this->_clientFacebook->createAuthUrl();
    }

    /**
     * @return string
     */
    public function getGoogleUrl()
    {
        return $this->_clientGoogle->createAuthUrl();
    }

    /**
     * @return string|void
     */
    public function getAppleUrl()
    {
        return $this->_clientApple->createAuthUrl();
    }

    /**
     * @return bool
     */
    public function isFacebookEnabled()
    {
        return $this->_clientFacebook->isEnabled();
    }

    /**
     * @return bool
     */
    public function isGoogleEnabled()
    {
        return $this->_clientGoogle->isEnabled();
    }

    /**
     * @return bool
     */
    public function isAppleEnabled()
    {
        return $this->_clientApple->isEnabled();
    }

    /**
     * @return bool
     */
    public function isButtonEnabledCheckout()
    {
        return $this->_sociallogin->isButtonEnabledCheckout();
    }

    /**
     * @return bool
     */
    public function isEnabledPopup()
    {
        return $this->_sociallogin->isButtonEnabledModal();
    }

    /**
     * @return bool
     */
    public function isEnabledInCreateAccount()
    {
        return $this->_sociallogin->isButtonEnabledCreateAccount();
    }
}
