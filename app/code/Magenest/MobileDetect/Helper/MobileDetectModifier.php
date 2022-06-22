<?php
namespace Magenest\MobileDetect\Helper;

use Detection\MobileDetect;

/**
 * Helper to be used for mobile detect and validations
 */
class MobileDetectModifier extends MobileDetect
{
    /**
     * MobileDetectModifier constructor.
     * @param null $headers
     * @param null $userAgent
     */
    public function __construct(
        $headers = null,
        $userAgent = null
    ) {
        if (!is_array($headers)) {
            $headers = [];
        }
        parent::__construct($headers, $userAgent);
    }
}
