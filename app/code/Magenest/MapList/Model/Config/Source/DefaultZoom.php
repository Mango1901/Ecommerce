<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/20/16
 * Time: 23:53
 */

namespace Magenest\MapList\Model\Config\Source;

class DefaultZoom implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => __('0')),
            array('value' => 1, 'label' => __('1')),
            array('value' => 2, 'label' => __('2')),
            array('value' => 3, 'label' => __('3')),
            array('value' => 4, 'label' => __('4')),
            array('value' => 5, 'label' => __('5')),
            array('value' => 6, 'label' => __('6')),
            array('value' => 7, 'label' => __('7')),
            array('value' => 8, 'label' => __('8')),
            array('value' => 9, 'label' => __('9')),
            array('value' => 10, 'label' => __('10')),
            array('value' => 11, 'label' => __('11')),
            array('value' => 12, 'label' => __('12')),
            array('value' => 13, 'label' => __('13')),
            array('value' => 14, 'label' => __('14')),
            array('value' => 15, 'label' => __('15')),
            array('value' => 16, 'label' => __('16')),
            array('value' => 17, 'label' => __('17')),
            array('value' => 18, 'label' => __('18')),
            array('value' => 19, 'label' => __('19')),
            array('value' => 20, 'label' => __('20')),
        );
    }
}
