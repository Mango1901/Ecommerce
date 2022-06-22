<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Api;

/**
 * Responsible for retrieving Reservation Quantity (without stock data)
 *
 * @api
 */
interface GetReservationsQuantityInterfaceV2
{
    /**
     * Given a product sku and a stock id, return reservation quantity
     *
     * @param string $sku
     * @param int $stockId
     * @return mixed
     */
    public function execute(string $sku, int $stockId, string $areaCode = null);
}
