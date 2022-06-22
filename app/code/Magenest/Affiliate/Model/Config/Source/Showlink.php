<?php


namespace Magenest\Affiliate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Showlink
 * @package Magenest\Affiliate\Model\Config\Source
 */
class Showlink implements ArrayInterface
{
    const SHOW_ON_TOP_LINK = 'top_link';
    const SHOW_ON_FOOTER_LINK = 'footer_link';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $optionArray[] = ['value' => '', 'label' => __('-- Please Select --')];

        foreach ($this->toArray() as $key => $value) {
            $optionArray[] = ['value' => $key, 'label' => $value];
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::SHOW_ON_FOOTER_LINK => __('Footer Link'), self::SHOW_ON_TOP_LINK => __('Top Link')];
    }
}
