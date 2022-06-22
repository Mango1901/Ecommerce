<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Model;

use Magenest\CustomInventoryReservation\Api\ReservationInterfaceV2;

/**
 * {@inheritdoc}
 *
 * @codeCoverageIgnore
 */
class ReservationModel implements ReservationInterfaceV2
{
    /**
     * @var int|null
     */
    private $reservationId;

    /**
     * @var int
     */
    private $stockId;

    /**
     * @var string
     */
    private $sku;

    /**
     * @var float
     */
    private $quantity;

    /**
     * @var string|null
     */
    private $metadata;
    /**
     * @var string | null
     */
    private $areacode;

    /**
     * @param int|null $reservationId
     * @param int $stockId
     * @param string $sku
     * @param float $quantity
     * @param null $metadata
     */
    public function __construct(
        $reservationId,
        int $stockId,
        string $sku,
        float $quantity,
        $metadata = null,
        string $areaCode = null
    ) {
        $this->reservationId = $reservationId;
        $this->stockId = $stockId;
        $this->sku = $sku;
        $this->quantity = $quantity;
        $this->metadata = $metadata;
        $this->areacode = $areaCode;
    }

    /**
     * @inheritdoc
     */
    public function getReservationId(): ?int
    {
        return $this->reservationId === null ?
            null:
            (int)$this->reservationId;
    }

    /**
     * @inheritdoc
     */
    public function getStockId(): int
    {
        return $this->stockId;
    }

    /**
     * @inheritdoc
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @inheritdoc
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata(): ?string
    {
        return $this->metadata;
    }

    public function getAreaCode(): ?string
    {
        return $this->areacode;
    }


}
