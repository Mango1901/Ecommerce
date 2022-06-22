<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 13/11/2020 16:34
 */

namespace Magenest\RewardPoints\Observer\Customer;

use Magenest\RewardPoints\Api\Data\RuleInterface;
use Magenest\RewardPoints\Helper\Data as PonitHelper;
use Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;
use Magenest\RewardPoints\Model\Rule;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;

class UpdateInformation implements ObserverInterface
{
    /**
     * @var PonitHelper
     */
    protected $_pointHelper;

    /**
     * @var RuleCollection
     */
    protected $_ruleCollection;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Json
     */
    protected $_jsonAction;

    /**
     * UpdateInformation constructor.
     * @param ManagerInterface $messageManager
     * @param Json $jsonAction
     * @param RuleCollection $ruleCollection
     * @param PonitHelper $pointHelper
     */
    public function __construct(
        ManagerInterface $messageManager,
        Json $jsonAction,
        RuleCollection $ruleCollection,
        PonitHelper $pointHelper
    ) {
        $this->_pointHelper = $pointHelper;
        $this->_ruleCollection = $ruleCollection;
        $this->_jsonAction = $jsonAction;
        $this->messageManager = $messageManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        try {
            $ruleCollection = $this->_ruleCollection->create()
                ->addFieldToFilter(RuleInterface::RULE_CONDITION_TYPE, Rule::CONDITION_CUSTOMER_FILL_FULL_DETAIL)
                ->addFieldToFilter(RuleInterface::RULE_STATUS, RuleInterface::RULE_STATUS_ACTIVE);

            if ($ruleCollection->getSize()) {
                $event = $observer->getEvent();

                /**
                 * @var Customer $customer
                 */
                $customer = $event->getCustomer();
                $rules = $ruleCollection->getItems();
                foreach ($rules as $rule) {
                    $listAttributes = empty($rule->getData(RuleInterface::RULE_CONDITION_VALUE)) ? [] : $this->_jsonAction->unserialize($rule->getData(RuleInterface::RULE_CONDITION_VALUE));

                    $isFullDetails = true;
                    foreach ($listAttributes['value'] as $attribute) {
                        if (empty($customer->getData($attribute))) {
                            $isFullDetails = false;
                            break;
                        }
                    }

                    // add points
                    if (!empty($listAttributes) && $isFullDetails) {
                        $this->_pointHelper->addPoints($customer, $rule->getData(RuleInterface::ENTITY_ID), null, null);
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->messageManager->addException($exception);
        }
    }
}