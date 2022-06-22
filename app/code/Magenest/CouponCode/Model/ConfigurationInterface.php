<?php
/**
 * Router. Matches action from request
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CouponCode\Model;

/**
 * Interface \Magento\Framework\App\RouterInterface
 *
 */
interface ConfigurationInterface
{

    /**
     * Apply
     *
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection $rules
     * @return Collection
     */
    public function apply($rules);
}
