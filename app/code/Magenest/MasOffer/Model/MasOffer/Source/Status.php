<?php

namespace Magenest\MasOffer\Model\MasOffer\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{

    const STATUS_0 = 0;
    const STATUS_1 = 1;
    const STATUS_2 = 2;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return
            [
                self::STATUS_0 => __('No'),
                self::STATUS_1 => __('Yes'),
                self::STATUS_2 => __('Completed'),
            ];
    }

    /**
     * Retrieve option array with empty value
     *
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
