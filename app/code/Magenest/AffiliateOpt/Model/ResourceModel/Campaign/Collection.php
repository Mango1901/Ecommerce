<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Campaign;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magenest\AffiliateOpt\Api\Data\CampaignSearchResultInterface;

/**
 * Class Collection
 * @api
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection implements CampaignSearchResultInterface
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
        $this->_init('Magenest\AffiliateOpt\Model\Campaign', 'Magenest\Affiliate\Model\ResourceModel\Campaign');
    }
}
