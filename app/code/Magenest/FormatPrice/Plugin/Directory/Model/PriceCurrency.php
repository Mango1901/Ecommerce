<?php

namespace Magenest\FormatPrice\Plugin\Directory\Model;


use Closure;
use Magento\Directory\Model\Currency;
use Magento\Framework\Locale\FormatInterface;

class PriceCurrency
{
    /**
     * @var FormatInterface
     */
    protected $_localeFormat;

    public function __construct(
        FormatInterface $localeFormat
    )
    {
        $this->_localeFormat = $localeFormat;
    }

    public function beforeFormatTxt(
        Currency $subject,
        $price,
        $options = []
    )
    {
        $options['precision'] = 0;
        return [$price, $options];
    }
}
