<?php

namespace Magenest\FastErp\Model\Block\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => 'Success',
                'value' => 1,
            ],
            [
                'label' => 'Fail',
                'value' => 0
            ]
        ];
    }
}
