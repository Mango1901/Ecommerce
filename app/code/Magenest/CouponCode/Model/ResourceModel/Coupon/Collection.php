<?php

namespace Magenest\CouponCode\Model\ResourceModel\Coupon;

use Magenest\CouponCode\Model\Coupon as CouponModel;
use Magenest\CouponCode\Model\ResourceModel\Coupon as CouponResourceModel;
use Magento\Framework\App\ResourceConnection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Constructor
     *
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Init collection
     */
    protected function _construct()
    {
        $this->_init(CouponModel::class, CouponResourceModel::class);
    }

    /**
     * Join with salesrule table and salesrule_customer_group table
     *
     * @return Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $resource = $this->_resource->getAssociatedEntitiesMap();
        if (isset($resource['customer_group']['rule_id_field'])) {
            $customerGroupField = $resource['customer_group']['rule_id_field'];
            $salesRuleCustomerGroup = $this->resourceConnection->getTableName('salesrule_customer_group');
            $salesRuleTable = $this->resourceConnection->getTableName('salesrule');
            $this->addFieldToSelect('code')
                ->addFieldToSelect('usage_limit')
                ->addFieldToSelect('coupon_id')
                ->addFieldToSelect('usage_per_customer');
            $this->getSelect()
                ->group('main_table.rule_id')
                ->joinLeft($salesRuleTable, 'main_table.rule_id =' .$salesRuleTable.'.rule_id')
                ->joinLeft($salesRuleCustomerGroup, $salesRuleTable.'.'.$customerGroupField.'=' .
                    $salesRuleCustomerGroup.'.'.$customerGroupField);
        }
        return $this;
    }
}
