<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 10/11/2020 16:32
 */

namespace Magenest\RewardPoints\Block\Customer;

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Api\Data\RuleInterface;
use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\Membership;
use Magenest\RewardPoints\Model\ResourceModel\Membership\Collection;
use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory as MembershipCollection;
use Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;
use Magenest\RewardPoints\Model\Rule;
use Magento\Framework\View\Element\Template;

class RewardProgram extends Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_coreConfig;

    /**
     * @var array
     */
    private $activeRules = [];

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var RuleCollection
     */
    protected $_ruleCollection;

    /**
     * @var \Zend_Filter_Interface
     */
    protected $templateProcessor;

    /**
     * RewardProgram constructor.
     * @param RuleCollection $ruleCollection
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param Template\Context $context
     * @param \Zend_Filter_Interface $templateProcessor
     * @param array $data
     */
    public function __construct(
        RuleCollection $ruleCollection,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        Template\Context $context,
        \Zend_Filter_Interface $templateProcessor,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_ruleCollection = $ruleCollection;
        $this->_coreConfig = $context->getScopeConfig();
        $this->priceHelper = $priceHelper;
        $this->templateProcessor = $templateProcessor;
    }

    public function getRewardProgramDescription()
    {
        return $this->_coreConfig->getValue(Data::XML_PATH_MEMBERSHIP_DESCRIPTION) ?? '';
    }

    public function getAddedValue(Membership $tier)
    {
        return $tier->getData(MembershipInterface::ADDED_VALUE_TYPE) == MembershipInterface::ADDED_VALUE_TYPE_FIXED ? $this->priceHelper->currency($tier->getData(MembershipInterface::ADDED_VALUE_AMOUNT)) : $tier->getData(MembershipInterface::ADDED_VALUE_AMOUNT) . '%';
    }

    public function getActiveRule()
    {
        if (empty($this->activeRules)) {
            $this->activeRules = $this->_ruleCollection->create()
                ->addFieldToFilter(RuleInterface::RULE_STATUS, RuleInterface::RULE_STATUS_ACTIVE)->getItems();
        }

        return $this->activeRules;
    }

    public function getRuleEarn(Rule $rule)
    {
        if ($rule->getData(RuleInterface::RULE_ACTION_TYPE) == RuleInterface::RULE_ACTION_TYPE_SPENT_X_GIVE_Y) {
            return __('Spend %1 get %2 point(s)', $this->priceHelper->currency($rule->getData(RuleInterface::STEPS)), $rule->getData(RuleInterface::POINTS));
        }

        return __('%1 point(s)', $rule->getData(RuleInterface::POINTS));
    }

    public function filterOutputHtml($string)
    {
        return $this->templateProcessor->filter($string);
    }
}