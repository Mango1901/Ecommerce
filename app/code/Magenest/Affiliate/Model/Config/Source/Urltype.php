<?php


namespace Magenest\Affiliate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Urltype
 * @package Magenest\Affiliate\Model\Config\Source
 */
class Urltype implements ArrayInterface
{
    const TYPE_HASH = 'hash';
    const TYPE_PARAM = 'param';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $array = [];
        foreach ($this->getOptionHash() as $key => $label) {
            $array[] = [
                'value' => $key,
                'label' => $label
            ];
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getOptionHash()
    {
        $array = [
            self::TYPE_HASH => __('Hash'),
            self::TYPE_PARAM => __('Parameter')
        ];

        return $array;
    }
}
