<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 15/12/2021
 * Time: 08:58
 */

namespace Magenest\OrderManagement\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\User\Model\ResourceModel\User\CollectionFactory;

class AdminUser implements OptionSourceInterface
{
    protected $sourceCollection;

    /**
     * SourceOption constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
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
            $result[] = [
                'label' => $value,
                'value' => $key ?: null
            ];
        }

        return $result;
    }

    public function getOptionArray()
    {
        $collection = $this->sourceCollection->create();

        $result[null] = __('System');
        foreach ($collection as $admin) {
            $result[$admin->getUserId()] = $admin->getName();
        }

        return $result;
    }

    public function getTextByOptionId($optionId)
    {
        $option = $this->getOptionArray();
        return isset($option[$optionId]) ? $option[$optionId] : '';
    }
}
