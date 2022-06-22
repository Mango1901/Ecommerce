<?php

namespace Magenest\Affiliate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CommissionDiscount extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'magenest_affiliate_commission_discount_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('magenest_affiliate_commission_discount', 'entity_id');
        $this->_useIsObjectNew = true;
    }
}
