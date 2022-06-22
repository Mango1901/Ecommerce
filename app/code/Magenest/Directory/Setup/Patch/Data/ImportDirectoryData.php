<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kootoro extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kootoro
 */

namespace Magenest\Directory\Setup\Patch\Data;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magenest\Directory\Model\CityFactory;
use Magenest\Directory\Model\DistrictFactory;
use Magenest\Directory\Model\WardFactory;

class ImportDirectoryData implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;
    /**
     * @var ReadFactory
     */
    private $readFactory;
    /**
     * @var CityFactory
     */
    private $cityFactory;
    /**
     * @var DistrictFactory
     */
    private $districtFactory;
    /**
     * @var WardFactory
     */
    private $wardFactory;


    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ComponentRegistrar $componentRegistrar,
        ReadFactory $readFactory,
        CityFactory $cityFactory,
        DistrictFactory $districtFactory,
        WardFactory $wardFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
        $this->cityFactory = $cityFactory;
        $this->districtFactory = $districtFactory;
        $this->wardFactory = $wardFactory;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $this->importDirectoryData();

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\ValidatorException
     * @throws \Zend_Json_Exception
     */
    private function importDirectoryData()
    {
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Magenest_Directory');
        $directoryRead = $this->readFactory->create($moduleDir);
        //Import city
        $fileAbsolutePath = $moduleDir . '/Setup/city.json';
        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
        $cities = $this->cityFactory->create()->getResource()->createMultiple(\Zend_Json::decode($directoryRead->readFile($filePath)));

        //Import district
        $fileAbsolutePath = $moduleDir . '/Setup/district.json';
        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
        $districts = $this->districtFactory->create()->getResource()->createMultiple(\Zend_Json::decode($directoryRead->readFile($filePath)), $cities);

        //Import ward
        $fileAbsolutePath = $moduleDir . '/Setup/ward.json';
        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
        $this->wardFactory->create()->getResource()->createMultiple(\Zend_Json::decode($directoryRead->readFile($filePath)), $districts);
    }
}
