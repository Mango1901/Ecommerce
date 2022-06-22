<?php
namespace Magenest\MobileDetect\Plugin;

use Magenest\Core\Model\Serializer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\View\DesignExceptions as InitialDesignExceptions;
use Magenest\MobileDetect\Helper\Detect;
use Magenest\MobileDetect\Helper\Redirect;

class DesignExceptions extends InitialDesignExceptions
{
    /** @var Detect */
    private $detect;

    /** @var Redirect */
    private $redirect;

    /** @var string */
    private $userAgent;

    /** @var Serializer */
    private $serializer;

    /**
     * DesignExceptions constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param string $exceptionConfigPath
     * @param string $scopeType
     * @param Detect $detect
     * @param Redirect $redirect
     * @param Serializer $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $exceptionConfigPath,
        $scopeType,
        Detect $detect,
        Redirect $redirect,
        Serializer $serializer
    ) {
        parent::__construct($scopeConfig, $exceptionConfigPath, $scopeType);
        $this->detect = $detect;
        $this->redirect = $redirect;
        $this->serializer = $serializer;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param HttpRequest $request
     * @return bool|string
     */
    public function aroundGetThemeByRequest($subject, callable $proceed, HttpRequest $request)
    {
        $rules = $proceed($request);

        $userAgent = $request->getServer('HTTP_USER_AGENT');

        if (empty($userAgent)) {
            return $rules;
        }

        $this->userAgent = $userAgent;

        if (!$this->redirect->isEnable()) {
            return $rules;
        }

        $mobileDetect = $this->detect->getMobileDetect();
        $mobileDetect->setHttpHeaders($request->getHeaders());
        $mobileDetect->setUserAgent($userAgent);

        $exception = $this->ifThemeChange();

        if (!$exception) {
            return $rules;
        }

        $expressions = $this->scopeConfig->getValue(
            $this->exceptionConfigPath,
            $this->scopeType
        );

        if (!$expressions) {
            return $rules;
        }

        $expressions = $this->serializer->unserialize($expressions);

        foreach ($expressions as $rule) {
            if (preg_match($rule['regexp'], $exception)) {
                return $rule['value'];
            }
        }

        return $rules;
    }

    /**
     * The tablet is overwritten by the mobile
     *
     * @return bool
     */
    private function ifThemeChange()
    {
        if ($this->detect->isTablet()) {
            $this->redirect->redirectTablet();
        }

        if ($this->detect->isMobile()) {
            $this->redirect->redirectMobile();
        }

        if ($this->detect->isDesktop()) {
            $this->redirect->redirectDesktop();
        }

        return $this->detect->isDetected();
    }
}
