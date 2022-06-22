<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 03/11/2020 13:54
 */

namespace Magenest\RewardPoints\Model\Membership;

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magento\Framework\Data\OptionSourceInterface;

class AddedValueType implements OptionSourceInterface
{
    const FIXED_TYPE_LABEL = 'Fixed Amount';

    const PERCENT_TYPE_LABEL = 'Percent of normal point';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => MembershipInterface::ADDED_VALUE_TYPE_FIXED,
                'label' => __(self::FIXED_TYPE_LABEL)
            ],
            [
                'value' => MembershipInterface::ADDED_VALUE_TYPE_PERCENT,
                'label' => __(self::PERCENT_TYPE_LABEL)
            ],
        ];
    }
}