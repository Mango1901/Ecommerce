<?php

namespace Magenest\AffiliateOpt\Helper;

use Magento\Framework\Api\SortOrder;
use Magenest\Affiliate\Helper\Data as AffiliateHelper;

/**
 * Class Data
 * @package Magenest\AffiliateOpt\Helper
 */
class Data extends AffiliateHelper
{
    /**
     * @return bool
     */
    public function canUseStoreSwitcherLayoutByMpReports()
    {
        if ($this->isModuleOutputEnabled('Magenest_Reports')) {
            return $this->getMpReportHelper()->isEnabled();
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getMpReportHelper()
    {
        return $this->objectManager->get('\Magenest\Reports\Helper\Data');
    }

    /**
     * @return array
     */
    public function getFilterDate()
    {
        $filterDate = [];
        if ($this->canUseStoreSwitcherLayoutByMpReports()) {
            $mpReportHelper = $this->getMpReportHelper();
            $dateRange = $mpReportHelper->getDateRange();
            $filterDate = $mpReportHelper->getDateTimeRangeFormat($dateRange[0], $dateRange[1], 1);
        }

        return $filterDate;
    }

    /**
     * @param $searchCriteria
     * @param $searchResult
     *
     * @return mixed
     */
    public function processGetList($searchCriteria, $searchResult)
    {
        if ($this->versionCompare('2.2.0')) {
            $collectionProcessor = $this->objectManager->get('\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
            $joinProcessor = $this->objectManager->get('\Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface');
            $collectionProcessor->process($searchCriteria, $searchResult);
            $joinProcessor->process($searchResult);
        } else {
            foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
                $this->addFilterGroupToCollection($filterGroup, $searchResult);
            }

            $sortOrders = $searchCriteria->getSortOrders();
            if ($sortOrders === null) {
                $sortOrders = [];
            }
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $field = $sortOrder->getField();
                $searchResult->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }

            $searchResult->setCurPage($searchCriteria->getCurrentPage());
            $searchResult->setPageSize($searchCriteria->getPageSize());
        }

        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}
