<?php


namespace Magenest\SocialLogin\Block\Widget;


use Magento\Framework\View\Element\Template;

/**
 * Class SocialLoginWidget
 * @package Magenest\SocialLogin\Block\Widget
 */
class SocialLoginWidget extends Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "Magenest_SocialLogin::widget/social_login.phtml";

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * SocialLoginWidget constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * @return bool
     */
    public function isLoggedIn() {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}
