<?php

namespace Magenest\AffiliateOpt\Ui\Component\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as AbstractProvider;
use Magenest\AffiliateOpt\Helper\Reports as ReportsHelper;

/**
 * Class DataProvider
 * @package Magenest\AffiliateOpt\Ui\Component\DataProvider
 */
class DataProvider extends AbstractProvider
{
    const AFFILIATE_TRANSACTION_LISTING_DATA_SOURCE = 'affiliate_transaction_listing_data_source';

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param ReportsHelper $reportsHelper
     * @param array $meta
     * @param array $data
     *
     * @throws NoSuchEntityException
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        ReportsHelper $reportsHelper,
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
        if ($this->name == self::AFFILIATE_TRANSACTION_LISTING_DATA_SOURCE) {
            $this->data['config']['labelColor'] = $reportsHelper->getLabelColorsStatus();
            $this->data['config']['priceFormat'] = $reportsHelper->getPriceFormat();
        }
    }

    /**
     * @param SearchResultInterface $searchResult
     *
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        if ($this->name == self::AFFILIATE_TRANSACTION_LISTING_DATA_SOURCE) {
            $arrItems = [];
            $arrItems['items'] = [];
            foreach ($searchResult->getItems() as $item) {
                $itemData = [];
                foreach ($item->getCustomAttributes() as $attribute) {
                    $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
                }
                $itemData['amount_report'] = $item->getAmount();
                $arrItems['items'][] = $itemData;
            }

            $arrItems['totalRecords'] = $searchResult->getTotalCount();

            return $arrItems;
        }

        return parent::searchResultToOutput($searchResult);
    }

    /**
     * Prepare Update Url
     */
    protected function prepareUpdateUrl()
    {
        if ($period = $this->request->getParam('period') !== null) {
            $this->data['config']['filter_url_params']['period'] = $period;
        }
        if ($store = $this->request->getParam('store') !== null) {
            $this->data['config']['filter_url_params']['store'] = $store;
        }
        if ($customer_group_id = $this->request->getParam('customer_group_id') !== null) {
            $this->data['config']['filter_url_params']['customer_group_id'] = $customer_group_id;
        }

        if ($this->request->getParam('startDate') !== null) {
            $this->data['config']['filter_url_params']['startDate'] = $this->request->getParam('startDate');
        }
        if ($this->request->getParam('endDate') !== null) {
            $this->data['config']['filter_url_params']['endDate'] = $this->request->getParam('endDate');
        }
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
    }
}
