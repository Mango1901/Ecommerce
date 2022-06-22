<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model\ResourceModel\Carrier\CSV;

use Magento\Framework\Phrase;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier\LocationDirectory;
use Magento\Framework\Exception\LocalizedException;
use Magenest\CustomTableRate\Model\KangarooTableRates;
use Magenest\CustomTableRate\Model\Express;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

/**
 * Class RowParser
 * @package Magenest\CustomTableRate\Model\ResourceModel\Carrier\CSV
 */
class RowParser
{
    /**
     * @var LocationDirectory
     */
    protected $_locationDirectory;

    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * RowParser constructor.
     * @param LocationDirectory $locationDirectory
     * @param SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        LocationDirectory $locationDirectory,
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->_locationDirectory = $locationDirectory;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * Retrieve columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return [
            'website_id',
            'method',
            'country_id',
            'city_id',
            'district_id',
            'from',
            'to',
            'fee',
        ];
    }

    /**
     * Parse provided row data.
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param int $websiteId
     * @param ColumnResolver $columnResolver
     * @param $method
     * @return array
     * @throws LocalizedException
     */
    public function parse(
        array $rowData,
        $rowNumber,
        $websiteId,
        ColumnResolver $columnResolver
    ) {
        if (count($rowData) < 6) {
            throw new LocalizedException(__('The Kangaroo Table Rates File Format is incorrect in row number "%1". Verify the format and try again.', $rowNumber));
        }

        return [
            'website_id' => $websiteId,
            'country_code' => 'VN',
            'city_code' => $this->_getCityCode($rowData, $rowNumber, $columnResolver),
            'city_id' => $this->_getCityId($rowData, $rowNumber, $columnResolver),
            'district_code' => $this->_getDistrictCode($rowData, $rowNumber, $columnResolver),
            'district_id' => $this->_getDistrictId($rowData, $rowNumber, $columnResolver),
            'source_code' => $this->_getInventorySourceCode($rowData, $rowNumber, $columnResolver, ColumnResolver::COLUMN_SOURCE_CODE),
            'weight' => $this->_getDecimalColumn($rowData, $rowNumber, $columnResolver, ColumnResolver::COLUMN_WEIGHT),
            'fee' => $this->_getDecimalColumn($rowData, $rowNumber, $columnResolver, ColumnResolver::COLUMN_PRICE)
        ];
    }

    /**
     * Get District Code from provided row data.
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param ColumnResolver $columnResolver
     * @return null|string
     * @throws LocalizedException
     */
    private function _getDistrictCode(array $rowData, $rowNumber, ColumnResolver $columnResolver)
    {
        $districtCode = $columnResolver->getColumnValue(ColumnResolver::COLUMN_DISTRICT, $rowData);
        $cityCode = $columnResolver->getColumnValue(ColumnResolver::COLUMN_CITY, $rowData);

        if (!$this->_locationDirectory->hasDistrictCode($districtCode, $cityCode)) {
            throw new LocalizedException(__('The "%1" district in row number "%2" is incorrect. Verify the district and try again.', $districtCode, $rowNumber));
        }

        return $districtCode;
    }

    /**
     * Get city id from provided row data.
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param ColumnResolver $columnResolver
     * @return null|int
     * @throws LocalizedException
     */
    private function _getDistrictId(array $rowData, $rowNumber, ColumnResolver $columnResolver)
    {
        $districtCode = $columnResolver->getColumnValue(ColumnResolver::COLUMN_DISTRICT, $rowData);
        $cityCode = $columnResolver->getColumnValue(ColumnResolver::COLUMN_CITY, $rowData);
        $districtData = $this->_locationDirectory->getDistrictByCode($districtCode, $cityCode);
        if (!$districtData) {
            throw new LocalizedException(__('The "%1" district in row number "%2" is incorrect. Verify the district and try again.', $districtCode, $rowNumber));
        }

        return (int)$districtData['district_id'];
    }

    /**
     * Get city code from provided row data.
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param ColumnResolver $columnResolver
     * @return null|string
     * @throws LocalizedException
     */
    private function _getCityCode(array $rowData, $rowNumber, ColumnResolver $columnResolver)
    {
        $cityCode = $columnResolver->getColumnValue(ColumnResolver::COLUMN_CITY, $rowData);
        if (!$this->_locationDirectory->hasCityCode($cityCode)) {
            throw new LocalizedException(__('The "%1" region/state in row number "%2" is incorrect. Verify the region/state and try again.', $cityCode, $rowNumber));
        }

        return $cityCode;
    }

    /**
     * Get city id from provided row data.
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param ColumnResolver $columnResolver
     * @return null|int
     * @throws LocalizedException
     */
    private function _getCityId(array $rowData, $rowNumber, ColumnResolver $columnResolver)
    {
        $cityCode = $columnResolver->getColumnValue(ColumnResolver::COLUMN_CITY, $rowData);
        $cityData = $this->_locationDirectory->getCityByCode($cityCode);
        if (!$cityData) {
            throw new LocalizedException(__('The "%1" region/state in row number "%2" is incorrect. Verify the region/state and try again.', $cityCode, $rowNumber));
        }

        return (int)$cityData['city_id'];
    }

    /**
     * Retrieve inventory source  from provided row data.
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param ColumnResolver $columnResolver
     * @param string $column
     * @return string
     * @throws LocalizedException
     */
    private function _getInventorySourceCode(array $rowData, $rowNumber, ColumnResolver $columnResolver, $column)
    {
        $sourceCode = $columnResolver->getColumnValue($column, $rowData);
        $source = $this->sourceRepository->get($sourceCode);

        if (!$source->getName()) {
            throw new LocalizedException(__('The "%1" source code in row number "%2" is incorrect. Verify the source code and try again.', $sourceCode, $rowNumber));
        }
        return $sourceCode;
    }

    /**
     * Retrieve price from provided row data.
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param ColumnResolver $columnResolver
     * @param string $column
     * @return bool|float
     * @throws LocalizedException
     */
    private function _getDecimalColumn(array $rowData, $rowNumber, ColumnResolver $columnResolver, $column)
    {
        $priceValue = $columnResolver->getColumnValue($column, $rowData);
        $price = $this->_parseDecimalValue($priceValue);
        if ($price === false) {
            throw new LocalizedException(__('The "%1" shipping price in row number "%2" is incorrect. Verify the shipping price and try again.', $priceValue, $rowNumber));
        }

        return $price;
    }

    /**
     * Parse and validate positive decimal value
     *
     * Return false if value is not decimal or is not positive
     *
     * @param string $value
     * @return bool|float
     */
    private function _parseDecimalValue($value)
    {
        $result = false;
        if (is_numeric($value)) {
            $value = (double)sprintf('%.4F', $value);
            if ($value >= 0.0000) {
                $result = $value;
            }
        }

        return $result;
    }
}
