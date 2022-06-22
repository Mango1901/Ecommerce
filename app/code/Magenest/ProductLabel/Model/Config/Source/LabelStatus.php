<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class LabelStatus
 * @package Magenest\ProductLabel\Model\Config\Source
 */
class LabelStatus implements OptionSourceInterface
{

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Inactive')],
            ['value' => 1, 'label' => __('Active')]
        ];
    }
}
