<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Plugin\Customer\Grid;

use Magento\Customer\Model\ResourceModel\Grid\Collection as CustomerCollection;

/**
 * Class Collection
 * @package Magenest\StoreCredit\Plugin\Customer\Grid
 */
class Collection
{
    /**
     * @param CustomerCollection $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetSelect(
        CustomerCollection $subject,
        $result
    ) {
        if ($result && !$subject->getFlag('is_magenest_store_credit_customer_joined')) {
            $table = $subject->getResource()->getTable('magenest_store_credit_customer');
            $result->joinLeft(
                ['mp_store_credit_customer' => $table],
                'mp_store_credit_customer.customer_id = main_table.entity_id'
            );
            $subject->setFlag('is_magenest_store_credit_customer_joined', true);
        }

        return $result;
    }
}
