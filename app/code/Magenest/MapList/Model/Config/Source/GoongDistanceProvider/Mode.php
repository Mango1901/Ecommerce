<?php

declare(strict_types=1);

namespace Magenest\MapList\Model\Config\Source\GoongDistanceProvider;

use Magento\Framework\Data\OptionSourceInterface;

class Mode implements OptionSourceInterface
{
    private const MODE_CAR = 'car';
    private const MODE_BIKE = 'bike';
    private const MODE_TAXI = 'taxi';
    private const MODE_TRUCK = 'truck';
    private const MODE_HD = 'hd';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MODE_CAR, 'label' => __('Car')],
            ['value' => self::MODE_BIKE, 'label' => __('Bike')],
            ['value' => self::MODE_TAXI, 'label' => __('Taxi')],
            ['value' => self::MODE_TRUCK, 'label' => __('Truck')],
            ['value' => self::MODE_HD, 'label' => __('Hd (for ride hailing vehicles)')]
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
