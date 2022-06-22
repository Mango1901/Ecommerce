<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Zend_Db_Select_Exception;
use Zend_Serializer_Exception;

/**
 * Class ProcessData
 * @package Magenest\AffiliateOpt\Model\ResourceModel
 */
class ProcessData extends AbstractCollection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('affiliate_commission', ['neq' => 'NULL']);
        $this->addDateToFilter()
            ->addStoreToFilter();

        return $this;
    }

    /**
     * @return int|null
     * @throws Zend_Serializer_Exception
     */
    public function getSize()
    {
        if ($this->_totalRecords === null) {
            $countSelect = clone $this->getSelect();
            $this->processData($countSelect);
        }

        return $this->_totalRecords;
    }

    /**
     * @return array|null
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Serializer_Exception
     */
    public function getData()
    {
        $this->_renderFilters()->_renderOrders()->_renderLimit();
        $select = $this->getSelect();
        $limit  = $select->getPart(Select::LIMIT_COUNT);
        $offSet = $select->getPart(Select::LIMIT_OFFSET);
        if ($this->_data === null) {
            $this->processData($select);
            $this->_afterLoadData();
        }

        if ($this->_totalRecords) {
            $this->sortData();
            $this->limitRecord($limit, $offSet);
        }

        return $this->_data;
    }

    /**
     * @param $select
     *
     * @return $this
     * @throws Zend_Serializer_Exception
     */
    public function processData($select)
    {
        $select->reset(Select::ORDER);
        $select->reset(Select::LIMIT_COUNT);
        $select->reset(Select::LIMIT_OFFSET);
        $select->columns([
            'period' => sprintf(
                '%s',
                $this->getConnection()->getDateFormatSql('main_table.created_at', $this->getPeriod())
            )
        ]);
        $items          = $this->_fetchAll($select);
        $itemsByAccount = $this->calculateItemByAccount($items);
        $this->processFilters($itemsByAccount);
        $this->_totalRecords = is_array($this->_data) ? count($this->_data) : 0;

        return $this;
    }

    /**
     * @param $itemsByAccount
     */
    public function processFilters($itemsByAccount)
    {
        foreach ($itemsByAccount as $period) {
            foreach ($period as $tier) {
                foreach ($tier as $row) {
                    $this->addEmailToData($row);
                }
            }
        }
    }

    /**
     * @param $row
     */
    public function addEmailToData($row)
    {
        $row['email'] = $this->helperData->getCustomerEmailByAccount($row['account_id']);
        if ($this->filterValue($row)) {
            $this->_data[] = $row;
        }
    }

    /**
     * @param $items
     *
     * @return array
     * @throws Zend_Serializer_Exception
     */
    public function calculateItemByAccount($items)
    {
        $itemsByAccount = [];
        foreach ($items as $item) {
            $item                = new DataObject($item);
            $affiliateCommission = $this->helperData->unserialize($item->getAffiliateCommission());
            if (is_array($affiliateCommission) && count($affiliateCommission)) {
                $totalSalesAmount      = $item->getBaseRowTotalInclTax() - $item->getBaseDiscountAmount() - $item->getBaseAffiliateDiscountAmount();
                $productName           = $item->getName();
                $period                = $item->getPeriod();
                $isCalculateSaleAmount = false;
                foreach ($affiliateCommission as $campaign) {
                    if ($isCalculateSaleAmount) {
                        $totalSalesAmount = 0;
                    }

                    foreach ($campaign as $tierId => $commission) {
                        if (!isset($itemsByAccount[$period])) {
                            $itemsByAccount[$period] = [];
                        }
                        if (!isset($itemsByAccount[$period]['tier' . $tierId])) {
                            $itemsByAccount[$period]['tier' . $tierId] = [];
                        }
                        if (isset($itemsByAccount[$period]['tier' . $tierId][$item->getSku()])) {
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['commission']         += $commission;
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['total_sales_amount'] += $totalSalesAmount;
                        } else {
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['account_id']         = $tierId;
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['sku']                = $item->getSku();
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['name']               = $productName;
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['commission']         = $commission;
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['total_sales_amount'] = $totalSalesAmount;
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['period']             = $period;
                            $itemsByAccount[$period]['tier' . $tierId][$item->getSku()]['item_id']            = $item->getItemId() . time() . rand();
                        }
                    }
                    $isCalculateSaleAmount = true;
                }
            }
        }

        return $itemsByAccount;
    }

    /**
     * @param $filters
     *
     * @return mixed
     */
    public function processExcludeFields($filters)
    {
        $excludeFields = ['placeholder', 'store_id', 'created_at', 'period'];
        foreach ($excludeFields as $excl) {
            if (isset($filters[$excl])) {
                unset($filters[$excl]);
            }
        }

        return $filters;
    }

    /**
     * @param $row
     *
     * @return $this|bool
     */
    public function filterValue($row)
    {
        $filters = $this->_request->getParam('filters');
        $flag    = true;
        if (is_array($filters)) {
            $filters = $this->processExcludeFields($filters);
            if (count($filters) > 0) {
                foreach ($filters as $field => $value) {
                    $tmpFlag = false;
                    if (is_array($value)) {
                        if (isset($value['from']) && isset($value['to'])) {
                            $tmpFlag = boolval($row[$field] >= $value['from'] && $row[$field] <= $value['to']);
                        } else {
                            if (isset($value['from'])) {
                                $tmpFlag = boolval($row[$field] >= $value['from']);
                            }
                            if (isset($value['to'])) {
                                $tmpFlag = boolval($row[$field] <= $value['to']);
                            }
                        }
                    } else {
                        $tmpFlag = stripos($row[$field], $value) !== false ? true : false;
                    }

                    $flag = $flag && $tmpFlag;
                }
            }
        }

        return $flag;
    }

    /**
     * @param $limit
     * @param $offSet
     */
    public function limitRecord($limit, $offSet)
    {
        if ($limit) {
            $offSet      = $offSet ?: 0;
            $this->_data = array_slice($this->_data, $offSet, $limit);
        }
    }

    /**
     * @return $this
     */
    public function sortData()
    {
        $sorting = $this->_request->getParam('sorting');
        if (is_array($sorting)) {
            $field     = $sorting['field'];
            $direction = $sorting['direction'];
            if ($field == 'period') {
                $this->sortPeriod($direction);
            } else {
                $this->sortValue($field, $direction);
            }
        }

        return $this;
    }

    /**
     * @param $direction
     */
    public function sortPeriod($direction)
    {
        if ($direction == 'desc') {
            usort($this->_data, function ($row1, $row2) {
                $time1 = $row1['period'];
                $time2 = $row2['period'];
                if (strtotime($time1) < strtotime($time2)) {
                    return 1;
                } elseif (strtotime($time1) > strtotime($time2)) {
                    return -1;
                } else {
                    return 0;
                }
            });
        } else {
            usort($this->_data, function ($row1, $row2) {
                return strtotime($row1['period']) - strtotime($row2['period']);
            });
        }
    }

    /**
     * @param $field
     * @param $direction
     */
    public function sortValue($field, $direction)
    {
        $direction = ($direction == 'desc') ? SORT_DESC : SORT_ASC;
        $values    = [];
        foreach ($this->_data as $key => $row) {
            $values[$key] = $row[$field];
        }
        array_multisort($values, $direction, $this->_data);
    }
}
