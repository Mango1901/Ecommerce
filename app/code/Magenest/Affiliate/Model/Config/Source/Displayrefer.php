<?php


namespace Magenest\Affiliate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Displayrefer
 * @package Magenest\Affiliate\Model\Config\Source
 */
class Displayrefer implements ArrayInterface
{
    const CATEGORY_PAGE = 'list';
    const PRODUCT_PAGE = 'detail';

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
        return [self::CATEGORY_PAGE => __('Category page'), self::PRODUCT_PAGE => __('Product page')];
    }
}
