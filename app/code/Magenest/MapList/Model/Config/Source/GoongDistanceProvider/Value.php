<?php

namespace Magenest\MapList\Model\Config\Source\GoongDistanceProvider;

use Magento\Framework\Data\OptionSourceInterface;

class Value implements OptionSourceInterface
{
    private const MODE_DISTANCE = 'distance';
    private const MODE_DURATION = 'duration';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MODE_DISTANCE, 'label' => __('Distance')],
            ['value' => self::MODE_DURATION, 'label' => __('Duration')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = $this->toOptionArray();
        $return = [];

        foreach ($options as $option) {
            $return[$option['value']] = $option['label'];
        }

        return $return;
    }
}
