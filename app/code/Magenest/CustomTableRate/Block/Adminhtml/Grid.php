<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier\CollectionFactory;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier\CSV\ColumnResolver;
use Magenest\Directory\Model\ResourceModel\City\CollectionFactory as CityCollectionFactory;
use Magenest\Directory\Model\ResourceModel\District\CollectionFactory as DistrictCollectionFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Grid
 * @package Magenest\CustomTableRate\Block\Adminhtml
 */
class Grid extends Extended
{
    /**
     * @var int
     */
    protected $_websiteId;

    /**
     * @var  string
     */
    protected $_method;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var CityCollectionFactory
     */
    protected $_cityCollectionFactory;

    /**
     * @var DistrictCollectionFactory
     */
    protected $_districtCollectionFactory;

    /**
     * @var SourceRepositoryInterface
     */
    protected $_sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param CityCollectionFactory $cityCollectionFactory
     * @param DistrictCollectionFactory $districtCollectionFactory
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        CollectionFactory $collectionFactory,
        CityCollectionFactory $cityCollectionFactory,
        DistrictCollectionFactory $districtCollectionFactory,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_cityCollectionFactory = $cityCollectionFactory;
        $this->_districtCollectionFactory = $districtCollectionFactory;
        $this->_sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('kangarooTableRateGrid');
        $this->_exportPageSize = 10000;
    }

    /**
     * Set current website
     *
     * @param int $websiteId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setWebsiteId($websiteId)
    {
        $this->_websiteId = $this->_storeManager->getWebsite($websiteId)->getId();

        return $this;
    }

    /**
     * Retrieve current website id
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWebsiteId()
    {
        if ($this->_websiteId === null) {
            $this->_websiteId = $this->_storeManager->getWebsite()->getId();
        }

        return $this->_websiteId;
    }

    /**
     * Set current method
     *
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->_method = $method;

        return $this;
    }

    /**
     * Retrieve method
     *
     * @return int
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magenest\CustomTableRate\Model\ResourceModel\Carrier\Collection */
        $collection = $this->_collectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'country_code',
            ['header' => __(ColumnResolver::COLUMN_COUNTRY), 'index' => 'country_code', 'default' => '*']
        );

        $this->addColumn(
            'city_code',
            ['header' => __(ColumnResolver::COLUMN_CITY), 'index' => 'city_code', 'default' => '*']
        );

        $this->addColumn(
            'district_code',
            ['header' => __(ColumnResolver::COLUMN_DISTRICT), 'index' => 'district_code', 'default' => '*']
        );

        $this->addColumn(
            'source_code',
            ['header' => __(ColumnResolver::COLUMN_SOURCE_CODE), 'index' => 'source_code', 'default' => '*']
        );

        $this->addColumn(
            'weight',
            ['header' => __(ColumnResolver::COLUMN_WEIGHT), 'index' => 'weight', 'default' => '*', 'sortable' => false]
        );

        $this->addColumn(
            'fee',
            ['header' => __(ColumnResolver::COLUMN_PRICE), 'index' => 'fee', 'default' => '*']
        );

        $this->addColumn(
            'city_name',
            ['header' => __(ColumnResolver::COLUMN_CITY_NAME), 'index' => 'city_name', 'default' => '']
        );

        $this->addColumn(
            'district_name',
            ['header' => __(ColumnResolver::COLUMN_DISTRICT_NAME), 'index' => 'district_name', 'default' => '']
        );

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('pk');
        $this->setMassactionIdFilter('pk');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('rates');
        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?')
        ]);
    }

    /**
     * Get district options
     *
     * @return array
     */
    public function getDistrictOptions()
    {
        $options = ['' => __('-- Please select district --')];
        /** @var \Magenest\Directory\Model\District $district */
        foreach ($this->_districtCollectionFactory->create() as $district) {
            $options[$district->getCode()] = $district->getDefaultName();
        }

        return $options;
    }

    /**
     * Get district options
     *
     * @return array
     */
    public function getInventorySourceOptions()
    {
        $options = ['' => __('-- Please select inventory name --')];
        $sources = $this->_sourceRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        foreach ($sources as $source) {
            $options[$source->getSourceCode()] = $source->getName();
        }

        return $options;
    }

    /**
     * Get city options
     *
     * @return array
     */
    public function getCityOptions()
    {
        $options = ['' => __('-- Please select city --')];
        /** @var \Magenest\Directory\Model\City $city */
        foreach ($this->_cityCollectionFactory->create() as $city) {
            if ($this->getRequest()->getFullActionName() == "adminhtml_rates_index") {
                $options[$city->getCode()] = $city->getDefaultName();
            } else {
                $options[$city->getCode()] = $city->getDefaultName();
            }
        }

        return $options;
    }
}
