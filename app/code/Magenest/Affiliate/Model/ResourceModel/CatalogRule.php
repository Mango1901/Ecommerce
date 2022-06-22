<?php
namespace Magenest\Affiliate\Model\ResourceModel;

use Magento\CatalogRule\Model\ResourceModel\Rule;

class CatalogRule extends Rule
{
    /**
     * @param $productId
     * @param $timestamp
     * @param $customerGroupId
     * @param $websiteId
     * @return array
     */
    public function getAffiliateRuleByProduct($productId, $timestamp, $customerGroupId, $websiteId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('catalogrule_product'))
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('product_id IN (?)', $productId)
            ->where('from_time = 0 or from_time < ?', $timestamp)
            ->where('to_time = 0 or to_time > ?', $timestamp)
            ->where('is_affiliate = ?', 1);

        return $connection->fetchAll($select);
    }
}
