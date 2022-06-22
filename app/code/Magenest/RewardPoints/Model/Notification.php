<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 15:06
 */

namespace Magenest\RewardPoints\Model;

use Magenest\RewardPoints\Api\Data\NotificationInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Notification extends AbstractModel implements IdentityInterface
{
    protected $_eventPrefix = NotificationInterface::TABLE_NAME;

    const CACHE_TAG = NotificationInterface::TABLE_NAME;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ResourceModel\Notification');
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}