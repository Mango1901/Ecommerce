<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_RewardPoints
 */

namespace Magenest\RewardPoints\Model;

use Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;

/**
 * Class Rule
 *
 * @package Magenest\RewardPoints\Model
 */
class Rule extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'reward_points_rule';

    const RULE_ACTION_TYPE_ADD       = 1;
    const RULE_ACTION_TYPE_ADDBYSTEP = 2;
    const REFUND_BY_REWARD_POINTS    = -1;
    const POINT_DEDUCT_BY_REFUND     = -4;
    const POINT_RETURN_BY_REFUND     = -5;
    const RULE_TYPE_PRODUCT          = 1;
    const RULE_TYPE_BEHAVIOR         = 2;
    const CONDITION_LIFETIME_AMOUNT  = 'lifetimeamount';
    const CONDITION_FIRST_PURCHASE   = 'firstpurchase';
    const CONDITION_LOGIN_DAILY   = 'login_daily';
    const CONDITION_EARN_WHEN_REFEREE_CLICKED   = 'earn_when_referee_clicked';
    const CONDITION_REGISTRATION_AFFILIATE   = 'registration_affiliate';
    const CONDITION_GRATEFUL = 'grateful';
    const CONDITION_CUSTOMER_FILL_FULL_DETAIL = 'filldetails';

    /**
     * @var
     */
    protected $_action_types;

    /**
     * @var string
     */
    protected $_eventPrefix = 'magenest_rewardpoints_rule_';

    /**
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * @var CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CombineFactory $combineFactory
     * @param CollectionFactory $actionCollectionFactory
     * @param RuleProductProcessor $ruleProductProcessor
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $relatedCacheTypes
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        CollectionFactory $actionCollectionFactory,
        RuleProductProcessor $ruleProductProcessor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $relatedCacheTypes = [],
        array $data = []
    ) {
        $this->_combineFactory          = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ResourceModel\Rule');
        $this->setIdFieldName('id');
        $this->_action_types = array(
            self::RULE_ACTION_TYPE_ADD       => __("Give X points to customer"),
            self::RULE_ACTION_TYPE_ADDBYSTEP => __("For each \$Y spent, give X points"),
        );
    }

    /**
     * @return array
     */
    public function ruleActionTypesToArray()
    {
        $actionTypes = [];
        $types       = $this->_registry->registry('rewardpoints_rule')->getRuleType();
        if ($types == self::RULE_TYPE_PRODUCT) {
            $actionTypes = $this->_action_types;
        } elseif ($types == self::RULE_TYPE_BEHAVIOR) {
            $actionTypes = [
                self::RULE_ACTION_TYPE_ADD => __("Give X points to customer"),
            ];
        }

        return $this->_toArray($actionTypes);
    }

    /**
     * @return array
     */
    public function ruleActionTypesToArrayOnRule()
    {
        $actionTypes = [];
        $types       = $this->getRuleType();
        if ($types == self::RULE_TYPE_PRODUCT) {
            $actionTypes = $this->_action_types;
        } elseif ($types == self::RULE_TYPE_BEHAVIOR) {
            $actionTypes = [
                self::RULE_ACTION_TYPE_ADD => __("Give X points to customer"),
            ];
        }

        return $this->_toArray($actionTypes);
    }

    /**
     * @param $array
     *
     * @return array
     */
    protected function _toArray($array)
    {
        $res = array();
        foreach ($array as $value => $label) {
            $res[$value] = $label;
        }

        return $res;
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection|\Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}
