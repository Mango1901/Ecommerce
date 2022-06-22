<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\RewardPoints\Setup;

use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory;
use Magenest\RewardPoints\Model\Rule;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class Recurring
 *
 * @package Magenest\RewardPoints\Setup
 */
class Recurring implements InstallSchemaInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductMetadata
     */
    protected $metadata;

    /**
     * @var \Magenest\RewardPoints\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serialize;

    /**
     * Recurring constructor.
     * @param \Magenest\RewardPoints\Model\RuleFactory $ruleFactory
     * @param ProductMetadata $metadata
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     */
    public function __construct(
        \Magenest\RewardPoints\Model\RuleFactory $ruleFactory,
        ProductMetadata $metadata,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize
    ) {
        $this->state             = $state;
        $this->metadata          = $metadata;
        $this->ruleFactory       = $ruleFactory;
        $this->collectionFactory = $collectionFactory;
        $this->serialize         = $serialize;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->correctConditionFormat();
        $this->checkModuleReferAFriend();
        $setup->endSetup();
    }

    private function correctConditionFormat()
    {
        $version        = $this->metadata->getVersion();
        $ruleCollection = $this->collectionFactory->create();

        $this->state->emulateAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL,
            function ($ruleCollection, $version) {
                /* @var Rule $rule */
                foreach ($ruleCollection as $rule) {
                    $condition = $rule->getData('conditions_serialized');
                    try {
                        if ($version < "2.2.0") {
                            $cond = json_decode($condition, true);
                            if (is_array($cond) && !empty($cond)) {
                                $rule->setData('conditions_serialized', $this->serialize->serialize($cond));
                                $rule->save();
                            }
                        } else {
                            if (isset($condition)) {
                                $cond = $this->serialize->unserialize($condition);
                                if (is_array($cond) && !empty($cond)) {
                                    $rule->setData('conditions_serialized', json_encode($cond));
                                    $rule->save();
                                }
                            } else {
                                return null;
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }
            },
            [$ruleCollection, $version]
        );
    }

    private function checkModuleReferAFriend()
    {
        $this->state->emulateAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL,
            function () {
                if (!Data::isReferAFriendModuleEnabled()) {
                    /** @var Rule $referralRule */
                    $referralRule = $this->ruleFactory->create();
                    $collection   = $referralRule->getCollection()->addFieldToFilter('condition', 'referafriend');

                    /** @var Rule $item */
                    foreach ($collection->getItems() as $item)
                        if (!empty($item->getData())) {
                            $item->setStatus(0)->save();
                        }
                }
            }
        );
    }
}
