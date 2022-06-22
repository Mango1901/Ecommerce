<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ReservationStockUi extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ReservationStockUi
 */

namespace Magenest\ReservationStockUi\Model\Source;

class Stock extends AbstractSource
{
    protected $stockCollection;

    public function __construct(
        \Magento\Inventory\Model\ResourceModel\Stock\CollectionFactory $stockCollection
    ) {
        $this->stockCollection = $stockCollection;
    }

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        /** @var \Magento\Inventory\Model\ResourceModel\Stock\Collection $col */
        $col = $this->stockCollection->create();
        $result = [];
        foreach ($col as $stock) {
            $result[$stock->getId()] = $stock->getName();
        }

        return $result;
    }
}
