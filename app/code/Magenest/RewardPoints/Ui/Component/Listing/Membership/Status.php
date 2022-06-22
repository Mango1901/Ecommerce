<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 30/11/2020 17:25
 */

namespace Magenest\RewardPoints\Ui\Component\Listing\Membership;

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!empty($item['is_active']) && $item['is_active'] == MembershipInterface::GROUP_STATUS_ENABLE) {
                    $item['is_active'] = '<span class="grid-severity-notice"><span>Active</span></span>';
                } else {
                    $item['is_active'] = '<span class="grid-severity-minor"><span>Inactive</span></span>';
                }
            }
        }

        return $dataSource;
    }
}