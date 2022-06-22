<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magenest\CustomTableRate\Model\ResourceModel;

use Magenest\CustomSource\Helper\Data;
use Magenest\CustomSource\Plugin\SetAreaCodeContext;
use Magenest\CustomTableRate\Model\KangarooTableRates;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier\Import;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory as InventorySourceCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Carrier
 * @package
 */
class Carrier extends AbstractDb
{
    /** @const - Table */
    const TABLE = 'kangaroo_shipping_tablerate';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @var Import
     */
    protected $_import;

    /**
     * @var Data
     */
    protected $sourceData;

    /**
     * @var InventorySourceCollectionFactory
     */
    private $_inventorySourceCollectionFactory;

    /**
     * Carrier constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param Import $import
     * @param Data $sourceData
     * @param InventorySourceCollectionFactory $_inventorySourceCollectionFactory
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        Import $import,
        Data $sourceData,
        InventorySourceCollectionFactory $_inventorySourceCollectionFactory,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_import = $import;
        $this->sourceData = $sourceData;
        $this->_inventorySourceCollectionFactory = $_inventorySourceCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, 'pk');
    }

    /**
     * Return table rate array or false by rate request
     *
     * @param DataObject $request
     * @return array|bool
     * @throws LocalizedException
     */
    public function getRate(DataObject $request)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())->limit(1);
        $select->where(
            'website_id = :website_id
            AND country_code = :country_code
            AND city_id = :city_id
            AND district_id = :district_id
            AND source_code = :source_code
            AND weight <= :weight'
        );
        $select->order('weight DESC');

        return $connection->fetchRow($select, [
            ':website_id' => (int)$request->getWebsiteId(),
            ':country_code' => $request->getDestCountryId(),
            ':city_id' => (int)$request->getDestCityId(),
            ':district_id' => (int)$request->getDestDistrictId(),
            ':source_code' => $this->getCurrentSourceByArea(),
            ':weight' => $request->getPackageWeight()
        ]);
    }

    /**
     * Import data
     *
     * @param array $values
     * @return void
     * @throws LocalizedException
     */
    private function _importData(array $values)
    {
        $connection = $this->getConnection();
        try {
            $connection->beginTransaction();

            if (count($values)) {
                $this->getConnection()->insertOnDuplicate(self::TABLE, $values);
                $connection->commit();
            }
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__('Something went wrong while importing table rates.'));
        }
    }

    /**
     * Upload table rate file and import data from it
     *
     * @param DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    public function uploadAndImport(DataObject $object)
    {
        /** @var \Magento\Framework\App\Config\Value $object */
        if (!empty($_FILES['groups']['tmp_name'][KangarooTableRates::CODE]['fields']['import']['value'])) {
            $filePath = $_FILES['groups']['tmp_name'][KangarooTableRates::CODE]['fields']['import']['value'];
        }

        if (!isset($filePath)) {
            return $this;
        }

        $websiteId = $this->_storeManager->getWebsite($object->getScopeId())->getId();
        $file = $this->_getCsvFile($filePath);

        try {
            foreach ($this->_import->getData($file, $websiteId) as $bunch) {
                $this->_importData($bunch);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        } finally {
            $file->close();
        }

        if ($this->_import->hasErrors()) {
            $error = __('We couldn\'t import this file because of these errors: %1', implode(" \n", $this->_import->getErrors()));
            throw new LocalizedException($error);
        }

        return $this;
    }

    /**
     * Get csv file
     *
     * @param string $filePath
     * @return \Magento\Framework\Filesystem\File\ReadInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function _getCsvFile($filePath)
    {
        $pathInfo = pathinfo($filePath);
        $dirName = $pathInfo['dirname'] ?? '';
        $fileName = $pathInfo['basename'] ?? '';

        $directoryRead = $this->_filesystem->getDirectoryReadByPath($dirName);

        return $directoryRead->openFile($fileName);
    }

    protected function getCurrentSourceByArea()
    {
        $area = $this->sourceData->getDefaultArea();
        $inventorySourceCollection = $this->_inventorySourceCollectionFactory->create();
        $inventorySourceCollection
            ->addFieldToFilter('area_code', $area);
        $source = $inventorySourceCollection->getFirstItem();
        return $source->getSourceCode() ?? 'default';
    }
}
