<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 02/11/2020 17:08
 */

namespace Magenest\RewardPoints\Model\Membership;

use Magenest\RewardPoints\Api\Data\MembershipInterface;

class CriteriaToReachTier implements \Magento\Framework\Data\OptionSourceInterface
{
    const DEFAULT_LABEL = 'Select condition';

    const REACH_BY_POINT_NUMBER_LABEL = 'Point number';

    const REACH_BY_SPEND_POINT_LABEL = 'Spend point';
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => MembershipInterface::GROUP_CONDITION_TYPE_BY_POINT_NUMBER,
                'label' => __(self::REACH_BY_POINT_NUMBER_LABEL)
            ],
            [
                'value' => MembershipInterface::GROUP_CONDITION_TYPE_BY_SPEND_POINT,
                'label' => __(self::REACH_BY_SPEND_POINT_LABEL)
            ],
        ];
    }
}