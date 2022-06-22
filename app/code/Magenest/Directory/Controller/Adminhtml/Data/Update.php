<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 07/05/2021
 * Time: 11:33
 */

namespace Magenest\Directory\Controller\Adminhtml\Data;

use Magenest\Directory\Model\ResourceModel\City\CollectionFactory as CityCollection;
use Magenest\Directory\Model\ResourceModel\District\CollectionFactory as DistrictCollection;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Magento\Framework\App\ResourceConnection;

class Update extends Action
{
    public const ADMIN_RESOURCE = "Magenest_Directory::configuration";

    protected $directoryList;

    protected $scopeConfig;

    protected $resource;

    protected $cityIdByCode;

    protected $districtIdByCode;

    public function __construct(
        DirectoryList $directoryList,
        ScopeConfigInterface $scopeConfig,
        Context $context,
        ResourceConnection $resourceConnection,
        CityCollection $cityCollection,
        DistrictCollection $districtCollection
    ) {
        $this->directoryList = $directoryList;
        $this->scopeConfig = $scopeConfig;
        $this->resource = $resourceConnection;
        foreach ($cityCollection->create() as $city) {
            $this->cityIdByCode[$city->getCode()] = $city->getCityId();
        }
        foreach ($districtCollection->create() as $district) {
            $this->districtIdByCode[$district->getCode()] = $district->getDistrictId();
        }
        parent::__construct($context);
    }

    public function execute()
    {
        $filePath = $this->scopeConfig->getValue('directory/update/file_upload');
        if (!$filePath) {
            $this->messageManager->addErrorMessage("Please upload the directory data first");
            return $this->_redirect('adminhtml/system_config/edit/section/directory');
        }
        $filePath = $this->directoryList->getPath(DirectoryList::MEDIA) . "/directory/" . $filePath;
        $data = IOFactory::load($filePath);
        $directoryData = $data->getActiveSheet()->toArray();
        unset($directoryData[0]);
        $city = [];
        $district = [];
        $ward = [];
        foreach ($directoryData as $row) {
            if (!isset($city[$row[1]]) && $row[1]) {
                $city[$row[1]] = [
                    'country_id' => 'VN',
                    'default_name' => $row[0],
                    'code' => $row[1],
                    'name' => $this->processCityName($row[0])
                ];
            }
            if (!isset($district[$row[3]]) && $row[2]) {
                $district[$row[3]] = [
                    'city_id' => $this->getCityId($row[1]),
                    'default_name' => $row[2],
                    'code' => $row[3],
                    'name' => $this->processDistrictName($row[2])
                ];
            }
            if (!isset($ward[$row[5]]) && $row[4]) {
                $ward[$row[5]] = [
                    'district_id' => $this->getDistrictId($row[3]),
                    'default_name' => $row[4],
                    'code' => $row[5],
                    'name' => $this->processWardName($row[4], $row[6])
                ];
            }
        }
        $connection = $this->resource->getConnection();
        if ($city) {
            $connection->insertOnDuplicate(
                $this->resource->getTableName('directory_city_entity'),
                $city,
                ['default_name', 'code', 'name']
            );
        }
        if ($district) {
            $connection->insertOnDuplicate(
                $this->resource->getTableName('directory_district_entity'),
                $district,
                ['default_name', 'code', 'name']
            );
        }
        if ($ward) {
            $connection->insertOnDuplicate(
                $this->resource->getTableName('directory_ward_entity'),
                $ward,
                ['default_name', 'code', 'name']
            );
        }
        $this->messageManager->addSuccessMessage("All directory data has been updated successfully!");
        return $this->_redirect('adminhtml/system_config/edit/section/directory');
    }

    protected function processCityName($city)
    {
        $prefixes = ['Thành phố ', 'Tỉnh '];
        return $this->removePrefixFromName($city, $prefixes);
    }

    protected function processDistrictName($district)
    {
        $prefixes = ['Quận ', 'Huyện ', 'Thành phố ', 'Thị xã '];
        return $this->removePrefixFromName($district, $prefixes);
    }

    protected function processWardName($ward, $wardLevel)
    {
        return $this->removePrefixFromName($ward, [$wardLevel . " "]);
    }

    protected function removePrefixFromName($name, $prefixes)
    {
        foreach ($prefixes as $prefix) {
            if (substr($name, 0, strlen($prefix)) == $prefix) {
                $name = substr($name, strlen($prefix));
                break;
            }
        }
        return $name;
    }

    protected function getCityId($cityCode)
    {
        return isset($this->cityIdByCode[$cityCode]) ? $this->cityIdByCode[$cityCode] : null;
    }

    protected function getDistrictId($districtCode)
    {
        return isset($this->districtIdByCode[$districtCode]) ? $this->districtIdByCode[$districtCode] : null;
    }
}
