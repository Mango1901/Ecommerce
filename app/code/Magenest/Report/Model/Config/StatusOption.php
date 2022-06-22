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

use Magenest\Report\Model\ResourceModel\Order\CollectionFactory;

class StatusOption implements \Magento\Framework\Data\OptionSourceInterface
{
    const DEFAULT_SOURCE_CODE = 'default';

    protected $sourceCollection;


    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
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

        $collection = $this->collectionFactory->create();
        $sourceList = [];
        foreach ($collection as $source) {
            $status = $source->getData('status');
            if(isset($status)) {
                $result[$status] = $status;
                array_push($sourceList,$status);
            }
        }
        if (!in_array(self::DEFAULT_SOURCE_CODE, $sourceList)) {
            unset($result[self::DEFAULT_SOURCE_CODE]);
        }

        return $result;
    }


}
