<?php

namespace Magenest\Affiliate\Model\ResourceModel\CommissionDiscount;

use Magenest\Affiliate\Model\CommissionDiscount as Model;
use Magenest\Affiliate\Model\ResourceModel\CommissionDiscount as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'magenest_affiliate_commission_discount_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
