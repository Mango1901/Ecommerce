<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 28/10/2021
 * Time: 15:45
 */

namespace Magenest\AffiliateCatalogRule\Model\ResourceModel;

use Magento\CatalogRule\Model\ResourceModel\Rule;

class AffiliateRule extends Rule
{
    public function getRulePrices(\DateTimeInterface $date, $websiteId, $customerGroupId, $productIds)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('catalogrule_product_price'), ['product_id', 'origin_rule_price'])
            ->where('rule_date = ?', $date->format('Y-m-d'))
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('product_id IN(?)', $productIds, \Zend_Db::INT_TYPE);

        return $connection->fetchPairs($select);
    }
}
