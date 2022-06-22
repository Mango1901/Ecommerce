<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Withdraw;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magenest\AffiliateOpt\Api\Data\WithdrawSearchResultInterface;

/**
 * Class Collection
 * @api
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Withdraw
 */
class Collection extends AbstractCollection implements WithdrawSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'withdraw_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'affiliate_withdraw_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'affiliate_withdraw_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AffiliateOpt\Model\Withdraw', 'Magenest\Affiliate\Model\ResourceModel\Withdraw');
    }
}
