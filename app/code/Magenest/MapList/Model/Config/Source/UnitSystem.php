<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/20/16
 * Time: 23:53
 */

namespace Magenest\MapList\Model\Config\Source;

class UnitSystem implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'google.maps.UnitSystem.METRIC', 'label' => __('Metric system (kilometers)')),
            array('value' => 'google.maps.UnitSystem.IMPERIAL', 'label' => __('Imperial system (miles)'))
        );
    }
}
