<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Accounts;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magenest\AffiliateOpt\Api\Data\AccountSearchResultInterface;

/**
 * Class Collection
 * @api
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Accounts
 */
class Collection extends AbstractCollection implements AccountSearchResultInterface
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
    protected $_eventPrefix = 'affiliate_account_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'affiliate_account_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AffiliateOpt\Model\Account', 'Magenest\Affiliate\Model\ResourceModel\Account');
    }
}
