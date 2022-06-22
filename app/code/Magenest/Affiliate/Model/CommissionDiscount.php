<?php

namespace Magenest\Affiliate\Model;

use Magenest\Affiliate\Model\ResourceModel\CommissionDiscount as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class CommissionDiscount extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'magenest_affiliate_commission_discount_model';

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
