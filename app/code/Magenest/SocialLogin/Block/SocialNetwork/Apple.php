<?php


namespace Magenest\SocialLogin\Block\SocialNetwork;

use Magento\Framework\View\Element\Template;
use Magenest\SocialLogin\Model\Apple\Client;

/**
 * Class Apple
 * @package Magenest\SocialLogin\Block\SocialNetwork
 */
class Apple extends Template
{
    /**
     * @var Client
     */
    protected $clientApple;

    /**
     * Apple constructor.
     * @param Client $clientApple
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct
    (
        Client $clientApple,
        Template\Context $context,
        array $data = []
    )
    {
        $this->clientApple = $clientApple;
        parent::__construct($context, $data);
    }

    /**
     * @return string|void
     */
    public function getButtonUrl()
    {
        return $this->_urlBuilder->getUrl("sociallogin/index/socialUrl",['social' => 'apple']);
    }

    /**
     * @return bool
     */
    public function isAppleEnabled()
    {
        return $this->clientApple->isEnabled();
    }
}
