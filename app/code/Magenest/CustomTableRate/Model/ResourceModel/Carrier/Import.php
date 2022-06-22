<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model\ResourceModel\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\ReadInterface;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier\CSV\ColumnResolver;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier\CSV\ColumnResolverFactory;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Config\Exception\LoaderLoadException;

/**
 * Import offline shipping.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Import
{
    /**
     * @var array
     */
    protected $_errors = [];

    /**
     * @var CSV\RowParser
     */
    protected $_rowParser;

    /**
     * @var CSV\ColumnResolverFactory
     */
    protected $_columnResolverFactory;

    /**
     * @var array
     */
    protected $_uniqueHash = [];

    /**
     * @var bool
     */
    protected $_imported = false;

    /**
     * Constructor.
     *
     * @param CSV\RowParser $rowParser
     * @param CSV\ColumnResolverFactory $columnResolverFactory
     */
    public function __construct(
        CSV\RowParser $rowParser,
        ColumnResolverFactory $columnResolverFactory
    )
    {
        $this->_rowParser = $rowParser;
        $this->_columnResolverFactory = $columnResolverFactory;
    }

    /**
     * Check if there are errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (bool)count($this->getErrors());
    }

    /**
     * Get errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Retrieve columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_rowParser->getColumns();
    }

    /**
     * Get data from file.
     *
     * @param ReadInterface $file
     * @param int $websiteId
     * @param string $method
     * @return \Generator
     * @throws LocalizedException
     */
    public function getData(ReadInterface $file, $websiteId)
    {
        if (!$this->_imported) {
            $this->_errors = [];
            $headers = $this->getHeaders($file);
            /** @var ColumnResolver $columnResolver */
            $columnResolver = $this->_columnResolverFactory->create(['headers' => $headers]);

            $rowNumber = 1;
            $items = [];
            while (false !== ($csvLine = $file->readCsv())) {
                try {
                    $rowNumber++;
                    if (empty($csvLine)) {
                        continue;
                    }

                    $rowData = $this->_rowParser->parse($csvLine, $rowNumber, $websiteId, $columnResolver);

                    $hash = $this->_getHash($rowData);
                    if (array_key_exists($hash, $this->_uniqueHash)) {
                        throw new LocalizedException(__('Duplicate Row #%1 (duplicates row #%2)', $rowNumber, $this->_uniqueHash[$hash]));
                    }

                    $this->_uniqueHash[$hash] = $rowNumber;
                    $items[] = $rowData;
                } catch (LocalizedException $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            $this->_imported = true;
            if (count($items)) {
                yield $items;
            }
        }
    }

    /**
     * Get hash string
     *
     * @param array $data
     * @return string
     */
    private function _getHash(array $data)
    {
        return sprintf("%s-%s-%s-%s-%0.2f", $data['source_code'], $data['country_code'], $data['city_code'], $data['district_code'], $data['weight']);
    }

    /**
     * Retrieve column headers.
     *
     * @param ReadInterface $file
     * @return array|bool
     * @throws LocalizedException
     */
    private function getHeaders(ReadInterface $file)
    {
        $headers = $file->readCsv();
        if ($headers === false || count($headers) < 5) {
            throw new LocalizedException(__('The Table Rates File Format is incorrect. Verify the format and try again.'));
        }

        return $headers;
    }
}
