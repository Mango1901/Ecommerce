<?php


namespace Magenest\Affiliate\Cron;

use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\TransactionFactory;

/**
 * Class CompleteHoldingCommission
 * @package Magenest\Affiliate\Cron
 */
class CompleteHoldingCommission
{
    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * MpCancelTranexpirydate constructor.
     *
     * @param Data $dataHelper
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        Data $dataHelper,
        TransactionFactory $transactionFactory
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_transactionFactory = $transactionFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $holdingDay = (int)$this->_dataHelper->getCommissionHoldingDays();
        if (!$this->_dataHelper->isEnabled() || !$holdingDay) {
            return;
        }

        $transactionModel = $this->_transactionFactory->create()->getCollection()
            ->addFieldToFilter('status', 2)
            ->addHoldingDaysToFilter($holdingDay);

        foreach ($transactionModel as $transaction) {
            $transaction->complete();
        }
    }
}
