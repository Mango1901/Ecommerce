<?php

namespace Magenest\SocialLogin\Model\Config;

use Magento\Backend\App\ConfigInterface;
use Magento\Backend\Block\Template\Context;
use Magenest\SocialLogin\Model\Facebook\Client as clientFacebook;
use Magenest\SocialLogin\Model\Google\Client as clientGoogle;
use Magenest\SocialLogin\Model\Apple\Client as clientApple;

/**
 * Class Config
 *
 * @package Magenest\SocialLogin\Model\Config
 */
class Config extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * @var clientFacebook
     */
    protected $clientFacebook;

    /**
     * @var clientGoogle
     */
    protected $clientGoogle;

    /**
     * @var clientApple
     */
    protected $clientApple;

    /**
     * @param Context $context
     * @param ConfigInterface $config
     * @param clientFacebook $clientFacebook
     * @param clientGoogle $clientGoogle
     * @param clientApple $clientApple
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        clientFacebook $clientFacebook,
        clientGoogle $clientGoogle,
        clientApple $clientApple,
        array $data = []
    ) {
        $this->_config = $config;
        $this->clientFacebook = $clientFacebook;
        $this->clientGoogle = $clientGoogle;
        $this->clientApple = $clientApple;
        parent::__construct($context, $data);
    }

    /**
     * create element for Access token field in store configuration
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $id = $element->getId();
        $copy = "var copyText = this;copyText.select();document.execCommand('copy');alert('Copied the Redirect Uri: ' + copyText.value);";
        switch ($id) {
            case 'magenest_credentials_facebook_redirect_uri':
                $url = $this->clientFacebook->getRedirectUri();
                $element->setData([
                    'value' => $url,
                    'tooltip' => 'Use this Redirect Uri value when creating your app',
                    'comment' => '<a href="https://developers.facebook.com/apps" target="_blank">Click here to navigate to Facebook\'s app page</a>',
                    'onclick' => $copy,
                ]);
                break;
            case 'magenest_credentials_google_redirect_uri':
                $url = $this->clientGoogle->getRedirectUri();
                $element->setData([
                    'value' => $url,
                    'tooltip' => 'Use this Redirect Uri value when creating your app',
                    'comment' => '<a href="https://console.developers.google.com" target="_blank">Click here to navigate to Google\'s app page</a>',
                    'onclick' => $copy,
                ]);
                break;
            case 'magenest_credentials_apple_redirect_uri':
                $url = $this->clientApple->getRedirectUri();
                $element->setData([
                    'value' => $url,
                    'tooltip' => 'Use this Redirect Uri value when creating your app',
                    'comment' => '<a href="https://developer.apple.com" target="_blank">Click here to Apple Dev\'s app page</a>',
                    'onclick' => $copy,
                ]);
                break;
        }
        return parent::_renderValue($element);
    }
}
