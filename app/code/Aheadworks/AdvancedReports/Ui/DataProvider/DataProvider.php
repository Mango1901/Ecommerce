<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Locale\FormatInterface;
use Aheadworks\AdvancedReports\Model\Filter;

/**
 * Class DataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var []
     */
    private $exportParams = [];

    /**
     * @var []
     */
    private $allowedRequestParams = [];

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var Filter\Store
     */
    private $storeFilter;

    /**
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param Reporting             $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param FormatInterface       $localeFormat
     * @param Filter\Store          $storeFilter
     * @param array                 $meta
     * @param array                 $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        FormatInterface $localeFormat,
        Filter\Store $storeFilter,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->localeFormat = $localeFormat;
        $this->storeFilter = $storeFilter;
        $this->applyDefaultFilters();
        $this->applyReportSettingFilters();
    }

    /**
     * Retrieve allowed params from request
     *
     * @return []
     */
    public function getAllowedRequestParams()
    {
        return $this->allowedRequestParams;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function prepareUpdateUrl()
    {
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            $addToFilter = true;
            $addToGridRowUrl = false;
            $decode = false;
            if (is_array($paramValue)) {
                $addToFilter = isset($paramValue['addToFilter']) ? $paramValue['addToFilter'] : $addToFilter;
                $decode = isset($paramValue['decode']) ? $paramValue['decode'] : $decode;
                $addToGridRowUrl = isset($paramValue['useParamInGridRowUrl'])
                    ? $paramValue['useParamInGridRowUrl']
                    : $addToGridRowUrl;
                $paramValue = $paramValue['value'];
            }
            if ('*' == $paramValue) {
                $paramValue = $this->request->getParam($paramName);
            }
            if ($paramValue) {
                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s/',
                    $this->data['config']['update_url'],
                    $paramName,
                    $paramValue
                );
                if ($addToGridRowUrl) {
                    $this->allowedRequestParams[$paramName] = $paramValue;
                }
                if ($addToFilter) {
                    $this->exportParams[$paramName] = $paramValue;
                    // For product variant performance report
                    if ($paramName == 'product_id') {
                        $parentId = $this->request->getParam('parent_id');
                        $paramValue = ['product_id' => $paramValue, 'parent_id' => $parentId];
                    }
                    if ($decode) {
                        $paramValue = base64_decode($paramValue);
                    }
                    $this->addFilter(
                        $this->filterBuilder
                            ->setField($paramName)
                            ->setValue($paramValue)
                            ->setConditionType('eq')
                            ->create()
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        /** @var \ $arrItems */
        $arrItems = [];
        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $arrItems['items'][] = $itemData;
        }
        $arrItems['totalRecords'] = $searchResult->getTotalCount();
        $arrItems['totals'][] = $searchResult->getTotals();
        $arrItems['priceFormat'] = $this->localeFormat->getPriceFormat(null, $this->storeFilter->getCurrencyCode());
        $arrItems['exportParams'] = $this->exportParams;

        $config = $this->data['config'];
        if (isset($config['displayChart']) && $config['displayChart']
        ) {
            $arrItems['chart']['rows'] = $searchResult->getChartRows();
        }

        return $arrItems;
    }

    /**
     * Add filters to SearchCriteria
     *
     * @return void
     */
    private function applyDefaultFilters()
    {
        $filter = $this->filterBuilder->setField('storeFilter')->create();
        $this->addFilter($filter);
        $filter = $this->filterBuilder->setField('periodFilter')->create();
        $this->addFilter($filter);
    }

    /**
     * Add report settings filters to SearchCriteria
     *
     * @return void
     */
    private function applyReportSettingFilters()
    {
        if ($reportSettings = $this->request->getParam('report_settings')) {
            foreach ($reportSettings as $settingName => $settingValue) {
                $filter = $this->filterBuilder
                    ->setField($settingName)
                    ->setValue($settingValue)
                    ->setConditionType('eq')
                    ->create();
                $this->addFilter($filter);
            }
        }
    }
}
