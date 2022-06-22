<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 04/02/2020
 * Time: 10:55
 */

namespace Magenest\MapList\Controller\Index;


use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class ListSource extends Action
{
    protected $sourceRepository;

    protected $searchCriteriaBuilder;

    public function __construct(
        Context $context,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ){
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $criteria = $this->createSearchCriteria();
        $sources = $this->sourceRepository->getList($criteria);
        $result = [];
        foreach ($sources->getItems() as $code => $source) {
            $result[$code] = $source->getData();
        }
        $result['length'] = $sources->getTotalCount();
        return $this->resultFactory->create('json')->setData($result);
    }

    protected function createSearchCriteria()
    {
        $params = $this->getRequest()->getParams();
        $this->searchCriteriaBuilder->addFilter('source_code','default', 'neq');
        if (isset($params['city']) && $params['city']) {
            $this->searchCriteriaBuilder->addFilter('city_id', $params['city']);
        }
        if (isset($params['region']) && $params['region']) {
            $this->searchCriteriaBuilder->addFilter('district_id', $params['region']);
        }
        return $this->searchCriteriaBuilder->create();
    }
}
