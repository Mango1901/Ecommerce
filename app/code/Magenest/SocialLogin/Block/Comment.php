<?php
namespace Magenest\SocialLogin\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Comment
 * @package Magenest\SocialLogin\Block
 */
class Comment extends Template
{

    /**
     * @var \Magenest\SocialLogin\Helper\SocialLogin
     */
    protected $socialLoginHelper;

    /**
     * Comment constructor.
     * @param \Magenest\SocialLogin\Helper\SocialLogin $socialLoginHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magenest\SocialLogin\Helper\SocialLogin $socialLoginHelper,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->socialLoginHelper = $socialLoginHelper;
    }

    /**
     * @return string
     */
    public function getFacebookAppId()
    {
        return $this->_scopeConfig->getValue('magenest/credentials/facebook/client_id');
    }

    /**
     * @return bool
     */
    public function enableComment()
    {
        return $this->socialLoginHelper->isButtonEnabledCommentProduct();
    }

    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrlCurrent()
    {
        $url =  $this->_storeManager->getStore()->getCurrentUrl();
        return substr($url, 0, strpos($url, ".html")+5);
    }
}
