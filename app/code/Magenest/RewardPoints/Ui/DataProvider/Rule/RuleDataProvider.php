<?php

namespace Magenest\RewardPoints\Ui\DataProvider\Rule;

use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\ResourceModel\Rule\Collection;
use Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class RuleDataProvider extends DataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * RuleDataProvider constructor.
     *
     * @param CollectionFactory $ruleFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $ruleFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $ruleFactory->create();
        if (!Data::isReferAFriendModuleEnabled())
            $this->collection->addFieldToFilter(['condition', 'condition'], [['null' => true], ['nlike' => 'referafriend']]);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }
}
