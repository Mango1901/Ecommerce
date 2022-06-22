<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\Report\Model\Config;

class SourceOption implements \Magento\Framework\Data\OptionSourceInterface
{
    const DEFAULT_SOURCE_CODE = 'default';

    protected $sourceCollection;

    /**
     * SourceOption constructor.
     *
     * @param \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $collectionFactory
    ) {
        $this->sourceCollection = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->getOptionArray() as $key => $value) {
            $option = [
                'label' => $value,
                'value' => $key
            ];
            if ($key == self::DEFAULT_SOURCE_CODE) {
                array_unshift($result, $option);
            } else {
                array_push($result, $option);
            }
        }

        return $result;
    }

    public function getOptionArray()
    {
        $result = [self::DEFAULT_SOURCE_CODE => "Default Source"];
        $collection = $this->sourceCollection->create()->addFieldToFilter('enabled', 1);
        $sourceList = [];
        foreach ($collection as $source) {
            $result[$source->getSourceCode()] = $source->getName();
            array_push($sourceList, $source->getSourceCode());
        }
        if (!in_array(self::DEFAULT_SOURCE_CODE, $sourceList)) {
            unset($result[self::DEFAULT_SOURCE_CODE]);
        }

        return $result;
    }

    public function getTextByOptionId($optionId)
    {
        $option = $this->getOptionArray();
        return isset($option[$optionId]) ? $option[$optionId] : '';
    }
}
