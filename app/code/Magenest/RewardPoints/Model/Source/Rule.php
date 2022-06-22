<?php

namespace Magenest\RewardPoints\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Rule
 * @package Magenest\RewardPoints\Model\Source
 */
class Rule extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Product Status values
     */
    const TYPE_BEHAVIOUR = 2;

    /**
     *
     */
    const TYPE_PRODUCT = 1;

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [self::TYPE_BEHAVIOUR => __('Behaviour Rule'), self::TYPE_PRODUCT => __('Product Rule')];
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
