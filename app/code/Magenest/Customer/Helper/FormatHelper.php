<?php

namespace Magenest\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class FormatHelper
 *
 */
class FormatHelper extends AbstractHelper
{
    const REGEX_FORMAT_TELEPHONE = '/([0-9]{3})([0-9]{4})([0-9]{3})/';

    /**
     * @param string $telephone
     * @return string
     */
    public function formatTelephoneVietnamese($telephone)
    {
        return preg_replace(self::REGEX_FORMAT_TELEPHONE, '$1 $2 $3', $telephone);
    }
}
