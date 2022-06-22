<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Api;

use Magento\Framework\Validation\ValidationException;

/**
 * Used to build ReservationInterface objects
 *
 * @api
 * @see ReservationBuilderInterfaceV2
 */
interface ReservationBuilderInterfaceV2
{
    /**
     * @param int $stockId
     * @return self
     */
    public function setStockId(int $stockId): self;

    /**
     * @param string $sku
     * @return self
     */
    public function setSku(string $sku): self;

    /**
     * @param float $quantity
     * @return self
     */
    public function setQuantity(float $quantity): self;

    /**
     * @param string|null $metadata
     * @return self
     */
    public function setMetadata(string $metadata = null): self;

    /**
     * @param string|null $areaCode
     * @return self
     */
    public function setAreaCode(string $areaCode = null): self;

    /**
     * @return ReservationInterfaceV2
     * @throws ValidationException
     */
    public function build(): ReservationInterfaceV2;
}
