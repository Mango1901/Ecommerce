<?php

namespace Magenest\RewardPoints\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Magenest\RewardPoints\Model\Source
 */
class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**
     *
     */
    const STATUS_ENABLED = 1;

    /**
     *
     */
    const STATUS_DISABLED = 0;

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [self::STATUS_ENABLED => __('Active'), self::STATUS_DISABLED => __('Inactive')];
    }

    /**
     * Retrieve option array with empty value
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
