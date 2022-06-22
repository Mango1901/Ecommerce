<?php

namespace Magenest\RewardPoints\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ReferralPoints
 */
class ReferralPoints extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Magenest\RewardPoints\Model\ResourceModel\ReferralPoints::class);
    }

}
