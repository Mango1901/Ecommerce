<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward Points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 29/10/2020 13:55
 */

namespace Magenest\RewardPoints\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Membership extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'magenest_rewardpoints_membership';

    /**
     * @inheritDoc
     */
    protected $_eventPrefix = 'magenest_rewardpoints_membership';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ResourceModel\Membership');
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}