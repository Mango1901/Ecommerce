<?php
namespace Magenest\MobileDetect\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magenest\MobileDetect\Helper\MobileDetectModifier as MobileDetect;

/**
 * Helper to be used for mobile detect and validations
 */
class Detect extends AbstractHelper
{

    const MOBILE = 'magenest_is_mobile';
    const TABLET = 'magenest_is_tablet';
    const DESKTOP = 'magenest_is_desktop';

    /** @var MobileDetect */
    private $mobileDetect;

    /** @var bool */
    private $detected = false;

    /**
     * Validations constructor.
     * @param Context $context
     * @param MobileDetect $mobileDetect
     */
    public function __construct(Context $context, MobileDetect $mobileDetect)
    {
        $this->mobileDetect = $mobileDetect;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isDetected()
    {
        return $this->detected;
    }

    /**
     * If is mobile device
     * @return bool
     */
    public function isMobile()
    {
        $this->detected = self::MOBILE;
        return $this->mobileDetect->isMobile();
    }

    /**
     * If is a tablet
     * @return bool
     */
    public function isTablet()
    {
        $this->detected = self::TABLET;
        return $this->mobileDetect->isTablet();
    }

    /**
     * If is desktop device
     * @return bool
     */
    public function isDesktop()
    {
        if ($this->isMobile()) {
            return false;
        }
        $this->detected = self::DESKTOP;
        return true;
    }

    /**
     * The mobile detect instance to be able to use all the functionality
     * @return MobileDetect
     */
    public function getMobileDetect()
    {
        return $this->mobileDetect;
    }
}
