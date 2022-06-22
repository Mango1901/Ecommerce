<?php

namespace Magenest\RewardPoints\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Magenest\RewardPoints\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var RewardPointSetupFactory
     */
    private $rewardPointSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * InstallData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param RewardPointSetupFactory $rewardPointSetupFactory
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        RewardPointSetupFactory $rewardPointSetupFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->eavSetupFactory         = $eavSetupFactory;
        $this->rewardPointSetupFactory = $rewardPointSetupFactory;
        $this->customerSetupFactory    = $customerSetupFactory;
        $this->attributeSetFactory     = $attributeSetFactory;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $eavSetup       = $this->eavSetupFactory->create(['setup' => $setup]);
        $customerSetup  = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var $attributeSet AttributeSet */
        $attributeSet     = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $rewardPointSetup = $this->rewardPointSetupFactory->create(['setup' => $setup]);

        $attributes = [
            'reward_point'       => ['type' => Table::TYPE_DECIMAL],
            'reward_amount'      => ['type' => Table::TYPE_DECIMAL],
            'base_reward_amount' => ['type' => Table::TYPE_DECIMAL],

        ];
        foreach ($attributes as $attributeCode => $attributeParams) {
            $rewardPointSetup->addAttribute('quote', $attributeCode, $attributeParams);

            $rewardPointSetup->addAttribute('quote_address', $attributeCode, $attributeParams);

            $rewardPointSetup->addAttribute('order', $attributeCode, $attributeParams);

            //perhaps invoice and creditmemo do not need hold data of reward point
            $rewardPointSetup->addAttribute('invoice', $attributeCode, $attributeParams);
            $rewardPointSetup->addAttribute('creditmemo', $attributeCode, $attributeParams);
        }


        $rewardPointSetup->addAttribute('quote', 'base_grand_total_without_reward', ['type' => Table::TYPE_DECIMAL]);
        $rewardPointSetup->addAttribute('quote', 'grand_total_without_reward', ['type' => Table::TYPE_DECIMAL]);

        $rewardPointSetup->addAttribute('quote_address', 'base_grand_total_without_reward', ['type' => Table::TYPE_DECIMAL]);
        $rewardPointSetup->addAttribute('quote_address', 'grand_total_without_reward', ['type' => Table::TYPE_DECIMAL]);

        //base_reward_points_invoiced
        $rewardPointSetup->addAttribute('order', 'base_reward_invoiced', ['type' => Table::TYPE_DECIMAL]);
        //reward_points_invoiced
        $rewardPointSetup->addAttribute('order', 'reward_invoiced', ['type' => Table::TYPE_DECIMAL]);

        //base_reward_points_refunded
        $rewardPointSetup->addAttribute('order', 'base_reward_refunded', ['type' => Table::TYPE_DECIMAL]);
        $rewardPointSetup->addAttribute('order', 'reward_refunded', ['type' => Table::TYPE_DECIMAL]);
        $installer->endSetup();
    }
}