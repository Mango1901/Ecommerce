<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model\ResourceModel\Carrier\CSV;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class ColumnResolver
 * @package Magenest\CustomTableRate\Model\ResourceModel\Carrier\CSV
 */
class ColumnResolver
{
    /** @const */
    const COLUMN_COUNTRY = 'Country Code';
    const COLUMN_CITY = 'City Code';
    const COLUMN_DISTRICT = 'District Code';
    const COLUMN_SOURCE_CODE = 'Inventory Source Code';
    const COLUMN_WEIGHT = 'Weight (And Above)';
    const COLUMN_PRICE = 'Shipping Price';
    const COLUMN_CITY_NAME = 'City Name';
    const COLUMN_DISTRICT_NAME = 'District Name';

    /**
     * @var array
     */
    protected $_nameToPositionIdMap = [
        self::COLUMN_COUNTRY => 0,
        self::COLUMN_CITY => 1,
        self::COLUMN_DISTRICT=> 2,
        self::COLUMN_SOURCE_CODE => 3,
        self::COLUMN_WEIGHT => 4,
        self::COLUMN_PRICE => 5,
        self::COLUMN_CITY_NAME => 6,
        self::COLUMN_DISTRICT_NAME => 7,
    ];

    /**
     * @var array
     */
    protected $_headers;

    /**
     * ColumnResolver constructor.
     * @param array $headers
     * @param array $columns
     */
    public function __construct(array $headers, array $columns = [])
    {
        $this->_nameToPositionIdMap = array_merge($this->_nameToPositionIdMap, $columns);
        $this->_headers = array_map('trim', $headers);
    }

    /**
     * Get column value
     *
     * @param string $column
     * @param array $values
     * @return string|int|float|null
     * @throws LocalizedException
     */
    public function getColumnValue($column, array $values)
    {
        $column = (string) $column;
        $columnIndex = array_search($column, $this->_headers, true);
        if (false === $columnIndex) {
            if (array_key_exists($column, $this->_nameToPositionIdMap)) {
                $columnIndex = $this->_nameToPositionIdMap[$column];
            } else {
                throw new LocalizedException(__('Requested column "%1" cannot be resolved', $column));
            }
        }

        if (!array_key_exists($columnIndex, $values)) {
            throw new LocalizedException(__('Column "%1" not found', $column));
        }

        if ($column === self::COLUMN_CITY && strlen($values[$columnIndex]) === 1) {
            $values[$columnIndex] = '0' . $values[$columnIndex];
        }

        if ($column === self::COLUMN_DISTRICT && strlen($values[$columnIndex]) === 1) {
            $values[$columnIndex] = '00' . $values[$columnIndex];
        } else if ($column === self::COLUMN_DISTRICT && strlen($values[$columnIndex]) === 2) {
            $values[$columnIndex] = '0' . $values[$columnIndex];
        }

        return  trim($values[$columnIndex]);
    }
}
