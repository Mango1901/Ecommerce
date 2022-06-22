<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Transaction;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magenest\AffiliateOpt\Api\Data\TransactionSearchResultInterface;

/**
 * Class Collection
 * @api
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection implements TransactionSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'transaction_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'affiliate_transaction_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'affiliate_transaction_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magenest\AffiliateOpt\Model\Transaction',
            'Magenest\Affiliate\Model\ResourceModel\Transaction'
        );
    }
}
