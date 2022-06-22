<?php

namespace Magenest\CouponCode\Model\ResourceModel\ClaimCoupon;

use Magenest\CouponCode\Model\ClaimCoupon as ClaimedCouponModel;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon as ClaimedCouponResourceModel;
use Magento\Framework\App\ResourceConnection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Constructor
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
        $this->_init(ClaimedCouponModel::class, ClaimedCouponResourceModel::class);
    }

    /**
     * Join with salesrule table and salesrule_coupon table
     *
     * @return Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $salesRuleCoupon = $this->resourceConnection->getTableName('salesrule_coupon');
        $salesRuleTable = $this->resourceConnection->getTableName('salesrule');
        $this->addFieldToSelect(['customer_id', 'coupon_id', 'times_used_per_customer', 'id', 'rule_id']);
        $this->getSelect()
            ->group('main_table.rule_id')
            ->joinLeft($salesRuleTable, 'main_table.rule_id =' .$salesRuleTable.'.rule_id')
            ->joinLeft($salesRuleCoupon, 'main_table.coupon_id =' .$salesRuleCoupon.'.coupon_id');
        return $this;
    }
}
