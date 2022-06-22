<?php

namespace Magenest\RewardPoints\Model;

use Magenest\RewardPoints\Api\Data\AccountInterface;
use Magenest\RewardPoints\Helper\MembershipAction;
use Magenest\RewardPoints\Model\ResourceModel\Account as AccountResource;
use Magenest\RewardPoints\Model\ResourceModel\Account\Collection as Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class Account
 * @package Magenest\RewardPoints\Model
 */
class Account extends AbstractModel
{
    /**
     * @var MembershipAction
     */
    protected $_membershipAction;

    /**
     * Account constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param AccountResource $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        MembershipAction $membershipAction,
        Context $context,
        Registry $registry,
        AccountResource $resource,
        Collection $resourceCollection,
        $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_membershipAction = $membershipAction;
    }

    /**
     * @inheritDoc
     */
    public function afterCommitCallback()
    {
        $storedPointsSpent = $this->getOrigData(AccountInterface::POINTS_SPENT) ?? 0;
        $storedPointsTotal = $this->getOrigData(AccountInterface::POINTS_TOTAL) ?? 0;

        if ($this->getData(AccountInterface::POINTS_SPENT) >= $storedPointsSpent || $this->getData(AccountInterface::POINTS_TOTAL) >= $storedPointsTotal) {
            $this->_membershipAction->setCustomerId($this->getData('customer_id'))->upgradeTier();
        }

        return parent::afterCommitCallback();
    }

}
