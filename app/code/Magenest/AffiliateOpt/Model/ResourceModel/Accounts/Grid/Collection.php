<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Accounts\Grid;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magenest\AffiliateOpt\Helper\Data;
use Magenest\AffiliateOpt\Model\ResourceModel\ProcessData;
use Psr\Log\LoggerInterface as Logger;
use Zend_Serializer_Exception;

/**
 * Class Collection
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Accounts\Grid
 */
class Collection extends ProcessData
{
    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $request
     * @param Data $helperData
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        Data $helperData,
        $mainTable = 'sales_order',
        $resourceModel = 'Magento\Sales\Model\ResourceModel\Order'
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $request,
            $helperData,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * @param $itemsByAccount
     */
    public function processFilters($itemsByAccount)
    {
        foreach ($itemsByAccount as $period) {
            foreach ($period as $tier) {
                $this->addEmailToData($tier);
            }
        }
    }

    /**
     * @param $orders
     *
     * @return array
     * @throws Zend_Serializer_Exception
     */
    public function calculateItemByAccount($orders)
    {
        $itemsByAccount = [];
        foreach ($orders as $item) {
            $item                = new DataObject($item);
            $affiliateCommission = $this->helperData->unserialize($item->getAffiliateCommission());
            if (is_array($affiliateCommission) && count($affiliateCommission)) {
                $totalSalesAmount = $item->getBaseGrandTotal();
                $totalOrder       = 1;
                $period           = $item->getPeriod();
                $isCalculate      = false;
                foreach ($affiliateCommission as $campaign) {
                    if ($isCalculate) {
                        $totalSalesAmount = $totalOrder = 0;
                    }
                    foreach ($campaign as $tierId => $commission) {
                        if (!isset($itemsByAccount[$period])) {
                            $itemsByAccount[$period] = [];
                        }
                        if (isset($itemsByAccount[$period]['tier' . $tierId])) {
                            $itemsByAccount[$period]['tier' . $tierId]['commission']         += $commission;
                            $itemsByAccount[$period]['tier' . $tierId]['total_sales_amount'] += $totalSalesAmount;
                            $itemsByAccount[$period]['tier' . $tierId]['number_of_order']    += $totalOrder;
                        } else {
                            $itemsByAccount[$period]['tier' . $tierId]['item_id']            = $item->getEntityId() . time();
                            $itemsByAccount[$period]['tier' . $tierId]['account_id']         = $tierId;
                            $itemsByAccount[$period]['tier' . $tierId]['commission']         = $commission;
                            $itemsByAccount[$period]['tier' . $tierId]['total_sales_amount'] = $totalSalesAmount;
                            $itemsByAccount[$period]['tier' . $tierId]['period']             = $period;
                            $itemsByAccount[$period]['tier' . $tierId]['number_of_order']    = $totalOrder;
                        }
                    }
                    $isCalculate = true;
                }
            }
        }

        return $itemsByAccount;
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return $this|void
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $customFilters = ['name', 'email', 'number_of_order', 'total_sales_amount', 'commission', 'period'];
        if (!in_array($field, $customFilters)) {
            parent::addFieldToFilter($field, $condition);
        }
    }
}
