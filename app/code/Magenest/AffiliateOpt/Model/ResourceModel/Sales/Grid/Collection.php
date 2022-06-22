<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Sales\Grid;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magenest\AffiliateOpt\Helper\Data;
use Magenest\AffiliateOpt\Model\ResourceModel\ProcessData;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Sales\Grid
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
        $mainTable = 'sales_order_item',
        $resourceModel = 'Magento\Sales\Model\ResourceModel\Order\Item'
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
     * @param array|string $field
     * @param null $condition
     *
     * @return $this|void
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $customFilters = ['name', 'email', 'total_sales_amount', 'commission', 'period'];
        if (!in_array($field, $customFilters)) {
            parent::addFieldToFilter($field, $condition);
        }
    }
}
