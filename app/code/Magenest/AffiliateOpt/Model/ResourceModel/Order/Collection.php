<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Order;

use Magento\Framework\DB\Select;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Order
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    /**
     * @param $affiliateAccount
     * @param $customerCollection
     * @param $transactionCollection
     * @param $date
     *
     * @return $this
     */
    public function jointSelectSql($affiliateAccount, $customerCollection, $transactionCollection, $date)
    {
        $this->getSelect()
            ->columns(['total_amount' => $this->getConnection()->getIfNullSql("SUM(main_table.base_grand_total)", 0)]);
        $affiliateAccount->getSelect()
            ->join(
                ['transaction' => $this->getTable('magenest_affiliate_transaction')],
                "main_table.account_id = transaction.account_id",
                ['*']
            )->group('main_table.account_id');

        $customerCollection->getSelect()
            ->join(
                ['aff_account' => $this->getTable('magenest_affiliate_account')],
                "e.entity_id = aff_account.customer_id AND aff_account.created_at >= '$date[0]' AND aff_account.created_at <= '$date[1]'",
                ['account_id', 'balance', 'total_commission', 'status']
            );
        $transactionCollection->getSelect()->reset(Select::COLUMNS);
        $transactionCollection->addFieldToSelect('account_id', 'tr_account_id')->addFieldToSelect('order_id');
        $transactionCollection->getSelect()->group(['order_id']);

        $this->getSelect()
            ->join(
                $this->getSqlString('tr', $transactionCollection),
                'main_table.entity_id = tr.order_id',
                ['tr_account_id']
            )
            ->joinRight(
                $this->getSqlString('ac', $customerCollection),
                'tr.tr_account_id = ac.account_id',
                ['account_id', 'name', 'email', 'balance', 'total_commission', 'status']
            )
            ->columns(['total_order' => 'COUNT(order_id)'])
            ->group('ac.account_id')
            ->order('ac.total_commission desc')
            ->limit(5);

        return $this;
    }

    /**
     * @param $alias
     * @param $collection
     *
     * @return array
     */
    public function getSqlString($alias, $collection)
    {
        return [$alias => new Zend_Db_Expr('(' . $collection->getSelect()->__toString() . ')')];
    }
}
