<?php

namespace Magenest\VietnameseUrlKey\Plugin;

use Magenest\VietnameseUrlKey\Model\VietnameseConverter;
use Magento\Framework\App\Config\ScopeConfigInterface;

class VietnameseTranslitUrl
{
    private $scopeConfig;

    private $convert;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        VietnameseConverter $converter
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->convert     = $converter;
    }

    public function beforeFilter(\Magento\Framework\Filter\TranslitUrl $subject, $string): string
    {
        if (!$this->scopeConfig->getValue('catalog/seo/convert_vietnamese_url')) {
            return $string;
        }
        return $this->convert->convert($string);
    }
}
