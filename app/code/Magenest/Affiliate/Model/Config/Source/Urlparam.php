<?php


namespace Magenest\Affiliate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Urlparam
 * @package Magenest\Affiliate\Model\Config\Source
 */
class Urlparam implements ArrayInterface
{
    const PARAM_ID = 'account_id';
    const PARAM_CODE = 'code';

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
            self::PARAM_CODE => __('Affiliate Code'),
            self::PARAM_ID => __('Affiliate ID')
        ];

        return $array;
    }
}
