<?php

namespace Magenest\PhotoReview\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MaxPhotoUpload
 */
class MaxPhotoUpload implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = [];
        foreach (range(1, 5) as $i) {
            $arr[] = [
                'value' => $i,
                'label' => $i
            ];
        }
        return $arr;
    }
}
