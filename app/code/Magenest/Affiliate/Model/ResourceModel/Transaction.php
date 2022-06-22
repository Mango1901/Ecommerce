<?php


namespace Magenest\Affiliate\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;
use Magenest\Affiliate\Model\Transaction\Status;
use Zend_Db_Expr;

/**
 * Class Transaction
 * @package Magenest\Affiliate\Model\ResourceModel
 */
class Transaction extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_affiliate_transaction', 'transaction_id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        if ($object->isObjectNew()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Update amount used for complete and onhold transaction
     *
     * @param $transaction
     * @param null $totalAmount
     *
     * @return $this
     * @throws LocalizedException
     */
    public function updateAmountUsed($transaction, $totalAmount = null)
    {
        if ($totalAmount === null) {
            $totalAmount = -$transaction->getAmount();
        }

        if ($totalAmount <= 0) {
            return $this;
        }

        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['transaction_id', 'amount', 'amount_used'])
            ->where('customer_id = :customer_id')
            ->where('status = :status')
            ->where('amount > amount_used');

        $binds = ['customer_id' => (int)$transaction->getCustomerId(), 'status' => Status::STATUS_COMPLETED];

        if ($transaction->getIsUpdateHoldingTransaction()) {
            $select->where('order_id = :order_id');
            $binds['order_id'] = (int)$transaction->getOrderId();
            $binds['status'] = Status::STATUS_HOLD;
        }

        $trans = $adapter->fetchAll($select, $binds);

        if (empty($trans) || !is_array($trans)) {
            return $this;
        }
        $usedIds = [];
        $lastId = 0;
        $lastUse = 0;
        foreach ($trans as $tran) {
            $availableAmount = $tran['amount'] - $tran['amount_used'];
            if ($totalAmount < $availableAmount) {
                $lastUse = $tran['amount_used'] + $totalAmount;
                $lastId = $tran['transaction_id'];
                $totalAmount = 0;
                break;
            }
            $totalAmount -= $availableAmount;
            $usedIds[] = $tran['transaction_id'];
            if ($totalAmount == 0) {
                break;
            }
        }

        // Update all depend transactions
        if (count($usedIds)) {
            $this->getConnection()->update(
                $this->getMainTable(),
                ['amount_used' => new Zend_Db_Expr('amount'), 'status' => Status::STATUS_COMPLETED],
                [new Zend_Db_Expr('transaction_id IN ( ' . implode(' , ', $usedIds) . ' )')]
            );
        }
        if ($lastId) {
            $this->getConnection()->update(
                $this->getMainTable(),
                ['amount_used' => new Zend_Db_Expr((string)$lastUse)],
                ['transaction_id = ?' => (int)$lastId]
            );
        }

        if ($transaction->getIsUpdateHoldingTransaction() && $totalAmount) {
            $transaction->setIsUpdateHoldingTransaction(false);
            $this->updateAmountUsed($transaction, $totalAmount);
        }

        return $this;
    }
}
