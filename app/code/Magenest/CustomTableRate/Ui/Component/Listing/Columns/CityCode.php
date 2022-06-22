<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\CustomTableRate\Ui\Component\Listing\Columns;

use Magenest\Directory\Model\ResourceModel\City\Collection;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CityCode
 */
class CityCode implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [
            ['label' => 'Default', 'value' => '']
        ];
        foreach ($this->collection->getItems() as $item) {
            $result[] = [
                'label' => $item->getData('name'),
                'value' => $item->getData('code'),
            ];
        }
        return $result;
    }
}
