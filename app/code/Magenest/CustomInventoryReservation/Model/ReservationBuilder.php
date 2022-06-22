<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Model;

use Magenest\CustomInventoryReservation\Api\ReservationInterfaceV2;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Validation\ValidationException;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magenest\CustomInventoryReservation\Api\ReservationBuilderInterfaceV2;
use \Magento\InventoryReservations\Model\SnakeToCamelCaseConverter;

/**
 * @inheritdoc
 */
class ReservationBuilder implements ReservationBuilderInterfaceV2
{
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
     * @var string
     */
    private $metadata;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var SnakeToCamelCaseConverter
     */
    private $snakeToCamelCaseConverter;

    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;
    /**
     * @var String
     */
    private $areacode;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param SnakeToCamelCaseConverter $snakeToCamelCaseConverter
     * @param ValidationResultFactory $validationResultFactory
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        SnakeToCamelCaseConverter $snakeToCamelCaseConverter,
        ValidationResultFactory $validationResultFactory
    ) {
        $this->objectManager = $objectManager;
        $this->snakeToCamelCaseConverter = $snakeToCamelCaseConverter;
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * @inheritdoc
     */
    public function setStockId(int $stockId): ReservationBuilderInterfaceV2
    {
        $this->stockId = $stockId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSku(string $sku): ReservationBuilderInterfaceV2
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setQuantity(float $quantity): ReservationBuilderInterfaceV2
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setMetadata(string $metadata = null): ReservationBuilderInterfaceV2
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function setAreaCode(string $areaCode = null): ReservationBuilderInterfaceV2
    {
        $this->areacode = $areaCode;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function build(): ReservationInterfaceV2
    {
        /** @var ValidationResult $validationResult */
        $validationResult = $this->validate();
        if (!$validationResult->isValid()) {
            throw new ValidationException(__('Validation error'), null, 0, $validationResult);
        }

        $data = [
            ReservationInterfaceV2::RESERVATION_ID => null,
            ReservationInterfaceV2::STOCK_ID => $this->stockId,
            ReservationInterfaceV2::SKU => $this->sku,
            ReservationInterfaceV2::QUANTITY => $this->quantity,
            ReservationInterfaceV2::METADATA => $this->metadata,
            ReservationInterfaceV2::AREA_CODE => $this->areacode
        ];

        $arguments = $this->convertArrayKeysFromSnakeToCamelCase($data);
        $reservation = $this->objectManager->create(ReservationInterfaceV2::class, $arguments);

        $this->reset();

        return $reservation;
    }

    /**
     * @return ValidationResult
     */
    private function validate()
    {
        $errors = [];

        if (null === $this->stockId) {
            $errors[] = __('"%field" is expected to be a number.', ['field' => ReservationInterface::STOCK_ID]);
        }

        if (null === $this->sku || '' === trim($this->sku)) {
            $errors[] = __('"%field" can not be empty.', ['field' => ReservationInterface::SKU]);
        }

        if (null === $this->quantity) {
            $errors[] = __('"%field" can not be null.', ['field' => ReservationInterface::QUANTITY]);
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }

    /**
     * Used to clean state after object creation
     * @return void
     */
    private function reset()
    {
        $this->stockId = null;
        $this->sku = null;
        $this->quantity = null;
        $this->metadata = null;
        $this->areacode = null;
    }

    /**
     * Used to convert database field names (that use snake case) into constructor parameter names (that use camel case)
     * to avoid to define them twice in domain model interface.
     *
     * @param array $array
     * @return array
     */
    private function convertArrayKeysFromSnakeToCamelCase(array $array): array
    {
        $convertedArrayKeys = $this->snakeToCamelCaseConverter->convert(array_keys($array));
        return array_combine($convertedArrayKeys, array_values($array));
    }
}
