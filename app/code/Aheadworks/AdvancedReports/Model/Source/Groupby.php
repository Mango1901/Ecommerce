<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Source;

/**
 * Class Groupby
 *
 * @package Aheadworks\AdvancedReports\Model\Source
 */
class Groupby implements \Magento\Framework\Option\ArrayInterface
{
    /**#@+
     * Constants defined for the source model
     */
    const TYPE_DAY = 'day';
    const TYPE_WEEK = 'week';
    const TYPE_MONTH = 'month';
    const TYPE_QUARTER = 'quarter';
    const TYPE_YEAR = 'year';
    /**#@-*/

    /**
     * @var []
     */
    private $options;

    /**
     * Get options
     *
     * @return []
     */
    public function getOptions()
    {
        return [
            ['value' => self::TYPE_DAY, 'label' => __('Day')],
            ['value' => self::TYPE_WEEK, 'label' => __('Week')],
            ['value' => self::TYPE_MONTH, 'label' => __('Month')],
            ['value' => self::TYPE_QUARTER, 'label' => __('Quarter')],
            ['value' => self::TYPE_YEAR, 'label' => __('Year')],
        ];
    }

    /**
     * To option array
     *
     * @return []
     */
    public function toOptionArray()
    {
        if ($this->options == null) {
            $this->options = [];
            foreach ($this->getOptions() as $value => $label) {
                $this->options[] = ['value' => $value, 'label' => $label];
            }
        }
        return $this->options;
    }
}
