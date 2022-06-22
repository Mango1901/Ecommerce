<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 29/10/2021
 * Time: 16:21
 */

namespace Magenest\RatingAttribute\Model\Attribute\Source;

class StarOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('5 Star'), 'value' => '5'],
                ['label' => __('4 Star'), 'value' => '4'],
                ['label' => __('3 Star'), 'value' => '3'],
                ['label' => __('2 Star'), 'value' => '2'],
                ['label' => __('1 Star'), 'value' => '1'],
            ];
        }
        return $this->_options;
    }
}
