<?php


namespace Magenest\Affiliate\Model\ResourceModel\Campaign\Api;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magenest\Affiliate\Api\Data\WithdrawSearchResultInterface;

/**
 * Class Collection
 * @api
 * @package Magenest\Affiliate\Model\ResourceModel\Campaign\Api
 */
class Collection extends AbstractCollection implements WithdrawSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'campaign_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'affiliate_campaign_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'affiliate_campaign_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magenest\Affiliate\Model\Api\Campaign',
            'Magenest\Affiliate\Model\ResourceModel\Campaign'
        );
    }
}
