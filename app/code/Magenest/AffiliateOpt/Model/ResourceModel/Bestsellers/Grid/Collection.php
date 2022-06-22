<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Bestsellers\Grid;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magenest\AffiliateOpt\Helper\Data;
use Magenest\AffiliateOpt\Model\ResourceModel\AbstractCollection;
use Psr\Log\LoggerInterface as Logger;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Transaction\Grid
 */
class Collection extends AbstractCollection
{
    /**
     * @var array
     */
    protected $_selectedColumns = [];

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
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()], $this->_getSelectedColumns());

        $this->addFieldToFilter('affiliate_commission', ['neq' => 'NULL'])
            ->addDateToFilter()
            ->addStoreToFilter();

        $this->getSelect()
            ->columns(['total_qty' => new Zend_Db_Expr('SUM(qty_ordered)')])
            ->group('sku');

        return $this;
    }

    /**
     * Retrieve selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        if (!$this->_selectedColumns) {
            $this->_selectedColumns = [
                'store_id'   => 'store_id',
                'item_id'    => 'item_id',
                'price'      => 'base_price',
                'created_at' => 'created_at',
                'name'       => 'name',
                'sku'        => 'sku'
            ];
        }

        return $this->_selectedColumns;
    }
}
