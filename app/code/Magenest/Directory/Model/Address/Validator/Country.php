<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model\Address\Validator;

use Magento\Directory\Helper\Data;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Model\AllowedCountries;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\ValidatorInterface;
use Magenest\Directory\Model\ResourceModel\City\CollectionFactory;

/**
 * Class Country
 * @package Magenest\Directory\Model\Address\Validator
 */
class Country implements ValidatorInterface
{
    /**
     * @var Data
     */
    protected $_directoryData;

    /**
     * @var AllowedCountries
     */
    protected $_allowedCountriesReader;

    /**
     * @var CollectionFactory
     */
    protected $_cityCollection;

    /**
     * Constructor.
     *
     * @param Data $directoryData
     * @param AllowedCountries $allowedCountriesReader
     * @param CollectionFactory $cityCollectionFactory
     */
    public function __construct(
        Data $directoryData,
        AllowedCountries $allowedCountriesReader,
        CollectionFactory $cityCollectionFactory
    ) {
        $this->_cityCollection = $cityCollectionFactory;
        $this->_directoryData = $directoryData;
        $this->_allowedCountriesReader = $allowedCountriesReader;
    }

    /**
     * @inheritdoc
     */
    public function validate(AbstractAddress $address)
    {
        $errors = $this->validateCountry($address);
        //by pass validate region from M1
//        if (empty($errors)) {
//            $errors = $this->validateRegion($address);
//        }

        return $errors;
    }

    /**
     * Validate country existence.
     *
     * @param AbstractAddress $address
     * @return array
     * @throws \Zend_Validate_Exception
     */
    private function validateCountry(AbstractAddress $address)
    {
        $countryId = $address->getCountryId();
        $errors = [];

        if (!\Zend_Validate::is($countryId, 'NotEmpty')) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'countryId']);
        } elseif (!in_array($countryId, $this->getWebsiteAllowedCountries($address), true)) {
            //Checking if such country exists.
            $errors[] = __(
                'Invalid value of "%value" provided for the %fieldName field.', ['fieldName' => 'countryId', 'value' => htmlspecialchars($countryId)]);
        }

        return $errors;
    }

    /**
     * Return allowed counties per website.
     *
     * @param AbstractAddress $address
     * @return array
     */
    private function getWebsiteAllowedCountries(AbstractAddress $address): array
    {
        $storeId = $address->getData('store_id');

        return $this->_allowedCountriesReader->getAllowedCountries(ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Validate region existence.
     *
     * @param AbstractAddress $address
     * @return array
     * @throws \Zend_Validate_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function validateRegion(AbstractAddress $address)
    {
        $countryId = $address->getCountryId();
        $regionCollection = $this->_cityCollection->create();
        $region = $address->getRegion();
        $regionId = (string)$address->getRegionId();
        $allowedRegions = $regionCollection->getAllIds();
        $isRegionRequired = $this->_directoryData->isRegionRequired($countryId);
        $errors = [];

        if ($isRegionRequired && empty($allowedRegions) && !\Zend_Validate::is($region, 'NotEmpty')) {
            //If region is required for country and country doesn't provide regions list
            //region must be provided.
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'region']);
        } elseif ($allowedRegions && !\Zend_Validate::is($regionId, 'NotEmpty') && $isRegionRequired) {
            //If country actually has regions and requires you to
            //select one then it must be selected.
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'regionId']);
        } elseif ($allowedRegions && $regionId && !in_array($regionId, $allowedRegions, true)) {
            //If a region is selected then checking if it exists.
            $errors[] = __(
                'Invalid value of "%value" provided for the %fieldName field.', ['fieldName' => 'regionId', 'value' => htmlspecialchars($regionId)]);
        }

        return $errors;
    }
}
