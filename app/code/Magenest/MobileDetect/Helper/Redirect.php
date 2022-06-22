<?php
namespace Magenest\MobileDetect\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Url\Validator;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ResponseFactory;

/**
 * Helper to be used for mobile detect and validations
 */
class Redirect extends AbstractHelper
{

    const MOBILEDETECT_ENABLED = 'magenest_mobiledetect/general/enabled';
    const MOBILEDETECT_MOBILE = 'magenest_mobiledetect/general/magenest_is_mobile';
    const MOBILEDETECT_TABLET = 'magenest_mobiledetect/general/magenest_is_tablet';
    const MOBILEDETECT_DESKTOP = 'magenest_mobiledetect/general/magenest_is_desktop';

    /** @var ScopeConfigInterface */
    private $config;

    /** @var ResponseFactory */
    private $responseFactory;

    /** @var Validator */
    private $validator;

    /**
     * Redirect constructor.
     * @param Context $context
     * @param ResponseFactory $responseFactory
     * @param Validator $validator
     */
    public function __construct(
        Context $context,
        ResponseFactory $responseFactory,
        Validator $validator
    ) {
        $this->config = $context->getScopeConfig();
        $this->responseFactory = $responseFactory;
        $this->validator = $validator;
        parent::__construct($context);
    }

    /**
     * Get config value
     *
     * @param string $configPath
     * @return string
     */
    public function getConfig($configPath)
    {
        return $this->config->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if module is enable
     *
     * @return boolean
     */
    public function isEnable()
    {
        return $this->getConfig(self::MOBILEDETECT_ENABLED);
    }

    /**
     * Redirect to tablet url
     */
    public function redirectTablet()
    {

        $tablet = $this->getConfig(self::MOBILEDETECT_TABLET);

        if ($this->validator->isValid($tablet)) {
            $this->responseFactory->create()->setRedirect($tablet)->sendResponse();
        }
    }

    /**
     * Redirect to mobile url
     */
    public function redirectMobile()
    {

        $mobile = $this->getConfig(self::MOBILEDETECT_MOBILE);

        if ($this->validator->isValid($mobile)) {
            $this->responseFactory->create()->setRedirect($mobile)->sendResponse();
        }
    }

    /**
     * Redirect to desktop url
     */
    public function redirectDesktop()
    {
        $desktop = $this->getConfig(self::MOBILEDETECT_DESKTOP);

        if ($this->validator->isValid($desktop)) {
            $this->responseFactory->create()->setRedirect($desktop)->sendResponse();
        }
    }
}
