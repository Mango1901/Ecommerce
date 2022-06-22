<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Migration\Job;

use Firebear\ImportExport\Model\Migration\AdditionalOptions;
use Firebear\ImportExport\Model\Migration\Config;
use Firebear\ImportExport\Model\Migration\DbConnection;
use Firebear\ImportExport\Model\Migration\Field\JobInterface as FieldJobInterface;
use Firebear\ImportExport\Model\Migration\FilterJobs\FilterJobsInterface;
use Firebear\ImportExport\Model\Migration\JobInterface;
use Firebear\ImportExport\Model\Migration\PostJobInterface;
use Firebear\ImportExport\Model\Migration\PreJobInterface;
use Magento\Framework\Exception\LocalizedException;
use Zend_Db_Statement_Interface;

/**
 * @inheritdoc
 */
class General implements JobInterface
{
    const INSERT_BATCH_SIZE = 1000;
    const PROCESS_BATCH_SIZE = 10000;

    /**
     * @var DbConnection
     */
    private $dbConnection;

    /**
     * @var string
     */
    private $sourceTable;

    /**
     * @var array
     */
    private $sourceJoins;

    /**
     * @var string
     */
    private $destinationTable;

    /**
     * @var array
     */
    private $sourceFields;

    /**
     * @var array
     */
    private $destinationFields;

    /**
     * @var array
     */
    private $deltaMigration;

    /**
     * @var array
     */
    private $fieldMapping;

    /**
     * @var array|FilterJobsInterface[]
     */
    private $sourceFilters = [];

    /**
     * @var FieldJobInterface[]
     */
    private $fieldJobs = [];

    /**
     * @var PreJobInterface[]
     */
    private $preJobs = [];

    /**
     * @var PostJobInterface[]
     */
    private $postJobs = [];

    /**
     * @var Config
     */
    private $prefixConfig;

    /**
     * @param DbConnection $dbConnection
     * @param string $sourceTable
     * @param string $destinationTable
     * @param array $sourceFields
     * @param array $destinationFields
     * @param array $deltaMigration
     * @param array $fieldMapping
     * @param array $sourceJoins
     * @param array $sourceFilters
     * @param array $fieldJobs
     * @param array $preJobs
     * @param array $postJobs
     * @param Config $prefixConfig
     */
    public function __construct(
        DbConnection $dbConnection,
        string $sourceTable,
        string $destinationTable,
        Config $prefixConfig,
        array $sourceFields,
        array $destinationFields,
        array $fieldMapping,
        array $deltaMigration = [],
        array $sourceJoins = [],
        array $sourceFilters = [],
        array $fieldJobs = [],
        array $preJobs = [],
        array $postJobs = []
    ) {
        $this->dbConnection = $dbConnection;
        $this->sourceTable = $sourceTable;
        $this->destinationTable = $destinationTable;
        $this->sourceFields = $sourceFields;
        $this->destinationFields = $destinationFields;
        $this->fieldMapping = $fieldMapping;
        $this->deltaMigration = $deltaMigration;
        $this->sourceJoins = $sourceJoins;
        $this->sourceFilters = $sourceFilters;
        $this->fieldJobs = $fieldJobs;
        $this->preJobs = $preJobs;
        $this->postJobs = $postJobs;
        $this->prefixConfig = $prefixConfig;
    }

    /**
     * @return DbConnection
     */
    protected function getDbConnection()
    {
        return $this->dbConnection;
    }

    /**
     * @return string
     */
    protected function getSourceTable()
    {
        $m1Prefix = $this->getPrefixConfig()->getM1Prefix();
        if ($m1Prefix) {
            return $m1Prefix . $this->sourceTable;
        }
        return $this->sourceTable;
    }

    /**
     * @return array
     */
    protected function getSourceJoins()
    {
        return $this->sourceJoins;
    }

    /**
     * @return string
     */
    protected function getDestinationTable()
    {
        $m2Prefix = $this->getPrefixConfig()->getM2Prefix();
        if ($m2Prefix) {
            return $m2Prefix . $this->destinationTable;
        }
        return $this->destinationTable;
    }

    /**
     * @return Config
     */
    protected function getPrefixConfig()
    {
        return $this->prefixConfig;
    }

    /**
     * @return array
     */
    protected function getSourceFields()
    {
        return $this->sourceFields;
    }

    /**
     * @return array
     */
    protected function getDestinationFields()
    {
        return $this->destinationFields;
    }

    /**
     * @return array
     */
    protected function getFieldMapping()
    {
        return $this->fieldMapping;
    }

    /**
     * @param string $destinationField
     *
     * @return string|null
     */
    protected function getSourceField($destinationField)
    {
        $sourceField = array_search($destinationField, $this->getFieldMapping());

        if ($sourceField !== false) {
            return $sourceField;
        }

        return null;
    }

    /**
     * @param string $sourceField
     *
     * @return string|null
     */
    protected function getDestinationField($sourceField)
    {
        $fieldMapping = $this->getFieldMapping();

        if (isset($fieldMapping[$sourceField])) {
            return $fieldMapping[$sourceField];
        }

        return null;
    }

    /**
     * @return array|FilterJobsInterface[]
     */
    protected function getSourceFilters()
    {
        return $this->sourceFilters;
    }

    /**
     * @return array|FieldJobInterface[]
     */
    protected function getFieldJobs()
    {
        return $this->fieldJobs;
    }

    /**
     * @param string $destinationField
     *
     * @return bool
     */
    protected function hasFieldJob($destinationField)
    {
        $fieldJobs = $this->getFieldJobs();

        return isset($fieldJobs[$destinationField]);
    }

    /**
     * @param string $destinationField
     *
     * @throws LocalizedException
     *
     * @return FieldJobInterface|mixed
     */
    protected function getFieldProcessor($destinationField)
    {
        $fieldJobs = $this->getFieldJobs();

        if (isset($fieldJobs[$destinationField])) {
            return $fieldJobs[$destinationField];
        }

        throw new LocalizedException(__("Job not found for the field %1", $destinationField));
    }

    /**
     * @return bool
     */
    protected function isSetDeltaMigration()
    {
        return !empty($this->deltaMigration)
            && isset($this->deltaMigration['active'])
            && $this->deltaMigration['active'];
    }

    /**
     * @return bool
     */
    protected function isSetDeltaMigrationLink()
    {
        return $this->isSetDeltaMigration() && !empty($this->deltaMigration['link']);
    }

    /**
     * @return PreJobInterface[]
     */
    protected function getPreJobs()
    {
        return $this->preJobs;
    }

    /**
     * @return PostJobInterface[]
     */
    protected function getPostJobs()
    {
        return $this->postJobs;
    }

    /**
     * Fetch data from the source
     *
     * @param int|null $page
     * @param AdditionalOptions|null $additionalOptions
     *
     * @throws LocalizedException
     *
     * @return Zend_Db_Statement_Interface
     */
    protected function fetchSourceData(
        $page = null,
        $additionalOptions = null
    ) {
        $sourceFields = array_values($this->getSourceFields());

        $sourceSelect = $this->getDbConnection()->getSourceChannel()
            ->select()
            ->from($this->getSourceTable(), $sourceFields);

        foreach ($this->getSourceJoins() as $tableAlias => $join) {
            $sourceSelect->joinLeft(
                [$tableAlias => $this->getPrefixConfig()->getM1Prefix() . $join['table']],
                $join['condition'],
                $join['fields']
            );
        }
        $sourceSelect->setPart('disable_staging_preview', true);

        foreach ($this->getSourceFilters() as $field => $filter) {
            if ($filter instanceof FilterJobsInterface) {
                $filter->apply($field, $sourceSelect);
            } else {
                $sourceSelect->where($filter);
            }
        }

        if ($this->isSetDeltaMigration()) {
            if (in_array('created_at', $sourceFields)
                && in_array('updated_at', $sourceFields)) {
                $sourceSelect->where(
                    "{$this->getSourceTable()}.created_at >= ? OR {$this->getSourceTable()}.updated_at >= ?",
                    $additionalOptions->getMigrateFromDate()
                );
            } elseif (in_array('created_at', $sourceFields)) {
                $sourceSelect->where(
                    "{$this->getSourceTable()}.created_at >= ?",
                    $additionalOptions->getMigrateFromDate()
                );
            } elseif ($this->isSetDeltaMigrationLink()) {
                $field = $this->deltaMigration['link']['field'];
                $linkField = $this->deltaMigration['link']['linkField'];
                $linkTable = $this->deltaMigration['link']['linkTable'];

                $linkSelect = $this->getDbConnection()->getSourceChannel()
                    ->select()
                    ->from($this->getPrefixConfig()->getM1Prefix() . $linkTable, [$linkField])
                    ->where('created_at >= ?', $additionalOptions->getMigrateFromDate())
                    ->orWhere('updated_at >= ?', $additionalOptions->getMigrateFromDate());

                $linkFieldIds = $this->getDbConnection()->getSourceChannel()->fetchCol($linkSelect);
                $sourceSelect->where("{$field} IN (?)", $linkFieldIds);
            }
        }

        if ($page !== null) {
            $sourceSelect->limit(
                self::PROCESS_BATCH_SIZE,
                self::PROCESS_BATCH_SIZE * ($page - 1)
            );
        }

        return $this->getDbConnection()->getSourceChannel()->query($sourceSelect);
    }

    /**
     * @inheritdoc
     */
    public function job($additionalOptions = null)
    {
        foreach ($this->getPreJobs() as $preJob) {
            $preJob->job();
        }

        $page = 1;

        while (true) {
            $sourceData = $this->fetchSourceData($page, $additionalOptions)->fetchAll();
            $destinationData = [];

            if (empty($sourceData)) {
                break;
            }

            foreach ($sourceData as $sourceDataRow) {
                $destinationRow = $this->jobRow($sourceDataRow);
                $destinationData[] = $destinationRow;
            }

            if (!empty($destinationData)) {
                $this->writeDestinationData($destinationData);
            }

            $page++;
        }

        if (!empty($destinationData)) {
            $this->writeDestinationData($destinationData);
        }

        foreach ($this->getPostJobs() as $postJob) {
            $postJob->job();
        }
    }

    /**
     * Process data row
     *
     * @param array $sourceDataRow
     *
     * @throws LocalizedException
     *
     * @return array
     */
    protected function jobRow($sourceDataRow)
    {
        $destinationRow = [];

        foreach ($this->getDestinationFields() as $destinationField) {
            $sourceField = $this->getSourceField($destinationField);

            if ($sourceField !== null) {
                $sourceValue = $sourceDataRow[$sourceField];
                $destinationValue = $sourceValue;
            } else {
                $sourceValue = null;
                $destinationValue = $sourceValue;
            }

            if ($this->hasFieldJob($destinationField)) {
                $fieldProcessor = $this->getFieldProcessor($destinationField);

                if ($fieldProcessor instanceof FieldJobInterface) {
                    $destinationValue = $fieldProcessor->job(
                        $sourceField,
                        $sourceValue,
                        $destinationField,
                        $destinationValue,
                        $sourceDataRow
                    );
                } else {
                    $destinationValue = $fieldProcessor;
                }
            }

            $destinationRow[$destinationField] = $destinationValue;
        }

        return $destinationRow;
    }

    /**
     * Write data to the destination
     *
     * @param array $destinationData
     *
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    protected function writeDestinationData($destinationData)
    {
        $this->getDbConnection()->getDestinationChannel()->query('SET FOREIGN_KEY_CHECKS = 0;');

        foreach (array_chunk($destinationData, self::INSERT_BATCH_SIZE) as $destinationBatch) {
            $this->getDbConnection()->getDestinationChannel()->insertOnDuplicate(
                $this->getDestinationTable(),
                $destinationBatch
            );
        }

        $this->getDbConnection()->getDestinationChannel()->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
