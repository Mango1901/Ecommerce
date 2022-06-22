<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Report\Ui\Component\DataProvider;

use Magenest\Report\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;


class DataProvider extends AbstractDataProvider
{

    protected $loadedData;
    protected $dataPersistor;
    protected $collection;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaInterface $searchCriteria
     * @param DataPersistorInterface $dataPersistor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ReportingInterface $reporting
     * @param SearchResultInterface $searchResult
     * @param array $meta
     * @param array $data
     */

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        SearchCriteriaInterface $searchCriteria,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataPersistorInterface $dataPersistor,
        ReportingInterface $reporting,
        SearchResultInterface $searchResult,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->searchCriteria = $searchCriteria;
        $this->searchResult = $searchResult;
        $this->reporting = $reporting;
        $this->dataPersistor = $dataPersistor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->name = $name;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return SearchCriteria|SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
        }
        return $this->searchCriteria;
    }




}
