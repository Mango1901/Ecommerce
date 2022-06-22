<?php
namespace Magenest\SocialLogin\Block\SocialNetwork;

use Magenest\SocialLogin\Model\Facebook\Client;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Facebook
 * @package Magenest\SocialLogin\Block\SocialNetwork
 */
class Facebook extends Template
{
    /**
     * @var Client
     */
    protected $_clientFacebook;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @param Client $clientFacebook
     * @param Session $customerSession
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Client $clientFacebook,
        Session $customerSession,
        Context $context,
        array $data = []
    ) {

        $this->_clientFacebook = $clientFacebook;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return parent::_construct
     */
    protected function _construct()
    {
        parent::_construct();
    }
    /**
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->_urlBuilder->getUrl("sociallogin/index/socialUrl",['social' => 'facebook']);
    }
    /**
     * @return Bool
     */
    public function isFacebookEnabled()
    {
        return $this->_clientFacebook->isEnabled();
    }
}
