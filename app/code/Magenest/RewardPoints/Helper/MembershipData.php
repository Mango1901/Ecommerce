<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 17/11/2020 15:58
 */

namespace Magenest\RewardPoints\Helper;

use Magenest\RewardPoints\Api\Data\AccountInterface;
use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Model\Account;
use Magenest\RewardPoints\Model\Membership;
use Magenest\RewardPoints\Model\ResourceModel\Membership\Collection;
use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory as MembershipCollection;
use Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer\CollectionFactory as MembershipCustomerCollection;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class MembershipData extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var null
     */
    protected $customerSelect = null;

    /**
     * @var MembershipCollection
     */
    protected $_membershipCollection;

    /**
     * @var MembershipCustomerCollection
     */
    protected $_membershipCustomerCollection;

    /**
     * @var \Magento\Framework\DataObject[]
     */
    private $activeTier = [];

    private $nextTier = null;

    /**
     * Data constructor.
     * @param MembershipCollection $membershipCollection
     * @param MembershipCustomerCollection $membershipCustomerCollection
     * @param Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        MembershipCollection $membershipCollection,
        MembershipCustomerCollection $membershipCustomerCollection,
        Context $context,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_membershipCollection = $membershipCollection;
        $this->_membershipCustomerCollection = $membershipCustomerCollection;
        $this->resource = $resource;
        $this->setCustomerSelect();
        parent::__construct($context);
    }

    /**
     * Set customer select object
     */
    public function setCustomerSelect()
    {
        $resource = $this->resource;
        $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $magenestRewardpointsAccountTable = $resource->getTableName('magenest_rewardpoints_account');
        $magenestRewardpointsMembershipCustomerTable = $resource->getTableName('magenest_rewardpoints_membership_customer');

        $select = $connection->select()->from(['mn_ra' => $magenestRewardpointsAccountTable])
            ->join(
                ['mn_mc' => $magenestRewardpointsMembershipCustomerTable],
                'mn_ra.customer_id = mn_mc.customer_id',
                'mn_mc.membership_id'
            );

        $this->customerSelect = $select;
    }

    /**
     * Get customer select object
     *
     * @return null
     */
    public function getCustomerSelect()
    {
        return clone $this->customerSelect;
    }

    /**
     * Get total earned points of all members in membership group
     * @deprecated since merge to Reward Point module
     *
     * @param $tier
     * @return mixed
     */
    public function getPointsTotalOnMemberTier($tier)
    {
        $resource = $this->resource;
        $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $select = $this->getCustomerSelect();
        $select->where('mn_mc.membership_id = ?', $tier);
        $select->columns(
            [
                'points_total' => new \Zend_Db_Expr('SUM(mn_ra.points_total)'),
            ]
        );

        return $connection->fetchRow($select)['points_total'];
    }

    /**
     * Get total spent points of all members in membership group
     * @deprecated since merge to Reward Point module
     *
     * @param $tier
     * @return mixed
     */
    public function getPointsSpentOnMemberTier($tier)
    {
        $resource = $this->resource;
        $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $select = $this->getCustomerSelect();
        $select->where('mn_mc.membership_id = ?', $tier);
        $select->columns(
            [
                'points_spent' => new \Zend_Db_Expr('SUM(mn_ra.points_spent)'),
            ]
        );

        return $connection->fetchRow($select)['points_spent'];
    }

    /**
     * Get average base subtotal order of membership group
     * @deprecated since merge to Reward Point module
     *
     * @param $tier
     * @return string
     */
    public function getCLV($tier)
    {
        $resource = $this->resource;
        $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $magenestRewardpointsMembershipCustomerTable = $resource->getTableName('magenest_rewardpoints_membership_customer');
        $select = $connection->select()->from(['mn_mc' => $magenestRewardpointsMembershipCustomerTable]);
        $select->where('mn_mc.membership_id = ?', $tier);
        $salesOrderTable = $resource->getTableName('sales_order');
        $select->join(
            ['so' => $salesOrderTable],
            'mn_mc.customer_id = so.customer_id',
            ['so.entity_id', 'so.base_subtotal']
        );

        $select1 = clone $select;
        $select2 = clone $select;

        $select1->columns(
            [
                'base_subtotal_so' => new \Zend_Db_Expr('SUM(so.base_subtotal)'),
            ]
        );

        $select2->columns(
            [
                'number' => new \Zend_Db_Expr('COUNT(DISTINCT so.entity_id)'),
            ]
        );

        $total = $connection->fetchRow($select1)['base_subtotal_so'];
        $number = $connection->fetchRow($select2)['number'];

        if (!$number) return null;

        return number_format($total / $number, 2);
    }

    /**
     * Get average base sub total invoice of each order in membership group
     * @deprecated since merge to Reward Point module
     *
     * @param $tier
     * @return null|string
     */
    public function getAOV($tier)
    {
        $resource = $this->resource;
        $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $magenestRewardpointsMembershipCustomerTable = $resource->getTableName('magenest_rewardpoints_membership_customer');
        $select = $connection->select()->from(['mn_mc' => $magenestRewardpointsMembershipCustomerTable]);
        $select->where('mn_mc.membership_id = ?', $tier);

        $salesOrderTable = $resource->getTableName('sales_order');
        $select->join(
            ['so' => $salesOrderTable],
            'mn_mc.customer_id = so.customer_id',
            ['so.entity_id']
        );

        $select1 = clone $select;

        $salesInvoiceTable = $resource->getTableName('sales_invoice');
        $select->join(
            ['si' => $salesInvoiceTable],
            'so.entity_id = si.order_id',
            ['si.base_subtotal']
        );

        $select->columns(
            [
                'base_subtotal_si' => new \Zend_Db_Expr('SUM(si.base_subtotal)'),
            ]
        );

        $select1->columns(
            [
                'number' => new \Zend_Db_Expr('COUNT(DISTINCT so.entity_id)'),
            ]
        );

        $total = $connection->fetchRow($select)['base_subtotal_si'];
        $number = $connection->fetchRow($select1)['number'];

        if (!$number) return null;

        return number_format($total / $number, 2);
    }

    /**
     * @param $customerId
     * @return \Magento\Framework\DataObject
     */
    public function getCustomerTier($customerId)
    {
        $membershipCustomer = $this->_membershipCustomerCollection->create()->joinMembership()
            ->addFieldToFilter(MembershipInterface::GROUP_STATUS, MembershipInterface::GROUP_STATUS_ENABLE)
            ->addFieldToFilter(MembershipCustomerInterface::CUSTOMER_ID, $customerId)->getFirstItem();

        return $membershipCustomer;
    }

    /**
     * @param Account $customer
     * @return float|int
     */
    public function getProgressPercent(Account $customer)
    {
        $activeTiers = $this->getActiveTier();
        $customerTierSortOrder = $this->getCustomerTier($customer->getData(AccountInterface::CUSTOMER_ID))->getData(MembershipInterface::SORT_ORDER) ?? 0;
        $percentAchieved = 0;
        if (!empty($customerTierSortOrder)) {
            $countTierReached = 0;
            $totalPointReached = 0;
            $spentPointReached = 0;
            $lastTier = end($activeTiers);
            if ($lastTier && $lastTier->getData(MembershipInterface::SORT_ORDER) == $customerTierSortOrder) {
                $percentAchieved = 100;
            } else {
                reset($activeTiers);
                foreach ($activeTiers as $tier) {
                    if ($tier->getData(MembershipInterface::SORT_ORDER) < $customerTierSortOrder) {
                        // customer doesn't reach this tier
                        $percentAchieved = $this->calculateProgress($tier, $customer, $countTierReached, $totalPointReached, $spentPointReached, count($activeTiers));
                        break;
                    }

                    // the tier that customer has reached
                    if ($tier->getData(MembershipInterface::GROUP_CONDITION_TYPE) == MembershipInterface::GROUP_CONDITION_TYPE_BY_POINT_NUMBER) {
                        $totalPointReached += $tier->getData(MembershipInterface::GROUP_CONDITION_VALUE);
                    } else {
                        $spentPointReached += $tier->getData(MembershipInterface::GROUP_CONDITION_VALUE);
                    }

                    $countTierReached++;
                }
            }
        } else {
            $firstTier = reset($activeTiers);
            $percentAchieved = $this->calculateProgress($firstTier, $customer, 0, 0, 0, count($activeTiers));
        }

        return $percentAchieved;
    }

    protected function calculateProgress($tier, $customer, $countTierReached, $totalPointReached, $spentPointReached, $totalTiers)
    {
        if (!$tier) {
            return 0;
        }

        if ($tier->getData(MembershipInterface::GROUP_CONDITION_TYPE) == MembershipInterface::GROUP_CONDITION_TYPE_BY_POINT_NUMBER) {
            $customerValue = $customer->getData(AccountInterface::POINTS_TOTAL) - $totalPointReached;

            $conditionValue = $tier->getData(MembershipInterface::GROUP_CONDITION_VALUE) - $totalPointReached;
        } else {
            $customerValue = $customer->getData(AccountInterface::POINTS_SPENT) - $spentPointReached;

            $conditionValue = $tier->getData(MembershipInterface::GROUP_CONDITION_VALUE) - $spentPointReached;
        }

        $percentInTier = $customerValue > $conditionValue ? 0 : $customerValue / $conditionValue;

        return ($countTierReached + $percentInTier) / (float)$totalTiers * 100;

    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getActiveTier()
    {
        if (empty($this->activeTier)) {
            $this->activeTier = $this->_membershipCollection->create()
                ->addFieldToFilter(MembershipInterface::GROUP_STATUS, MembershipInterface::GROUP_STATUS_ENABLE)
                ->addOrder(MembershipInterface::SORT_ORDER, Collection::SORT_ORDER_DESC)->getItems();
        }

        return $this->activeTier;
    }

    /**
     * @param $customerId
     * @return \Magento\Framework\Phrase|string
     */
    public function getNextTierCondition($customerId)
    {
        $nextTier = $this->getNextTier($customerId);
        if ($nextTier) {
            if ($nextTier->getData(MembershipInterface::GROUP_CONDITION_TYPE) == MembershipInterface::GROUP_CONDITION_TYPE_BY_POINT_NUMBER) {
                return __('Earn <strong>%1 Reward Points</strong> to get to %2', $nextTier->getData(MembershipInterface::GROUP_CONDITION_VALUE), $nextTier->getData(MembershipInterface::GROUP_NAME));
            }
            return __('Spend <strong>%1 Reward Points</strong> to get to %2', $nextTier->getData(MembershipInterface::GROUP_CONDITION_VALUE), $nextTier->getData(MembershipInterface::GROUP_NAME));
        }

        return '';
    }

    /**
     * @param $customerId
     * @return \Magento\Framework\DataObject
     */
    public function getNextTier($customerId)
    {
        $customerTierSortOrder = $this->getCustomerTier($customerId)->getData(MembershipInterface::SORT_ORDER) ?? 0;
        if ($this->nextTier == null) {
            $activeTier = $this->getActiveTier();
            if ($customerTierSortOrder == 0) {
                $this->nextTier = reset($activeTier);
            } else {
                foreach ($activeTier as $tier) {
                    if ($tier->getData(MembershipInterface::SORT_ORDER) < $customerTierSortOrder) {
                        $this->nextTier = $tier;
                        break;
                    }
                }
            }
        }

        return $this->nextTier;
    }

    public function isEnableMembership()
    {
        return $this->scopeConfig->isSetFlag(Data::XML_PATH_MEMBERSHIP_STATUS);
    }

    public function getWysiwygHtmlContent($content)
    {

    }
}
