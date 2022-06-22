<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Model\Source\Config;

use Magento\Framework\Option\ArrayInterface;
use Magenest\MegaMenu\Model\ResourceModel\MegaMenu\CollectionFactory;

/**
 * Class Menu
 * @package Magenest\MegaMenu\Model\Source\Config
 */
class Menu implements ArrayInterface
{
    /** @var  CollectionFactory */
    protected $collectionFactory;

    /**
     * Constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $result = [['value' => '', 'label' => __('-- Please Select --')]];

        /** @var \Magenest\MegaMenu\Model\MegaMenu $menu */
        foreach ($this->collectionFactory->create() as $menu) {
            $result[] = [
                'label' => $menu->getMenuName(),
                'value' => $menu->getId()
            ];
        }

        return $result;
    }
}
