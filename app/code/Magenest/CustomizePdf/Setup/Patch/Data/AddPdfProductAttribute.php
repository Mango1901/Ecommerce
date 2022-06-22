<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 20/10/2021
 * Time: 11:44
 */

namespace Magenest\CustomizePdf\Setup\Patch\Data;

use Magento\Catalog\Model\Config;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddPdfProductAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    private $attributeManagement;

    private $config;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AttributeManagementInterface $attributeManagement
     * @param Config $config
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        AttributeManagementInterface $attributeManagement,
        Config $config,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeManagement = $attributeManagement;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute('catalog_product', 'product_instruction_pdf', [
            'type' => 'text',
            'label' => 'Product Instruction PDF',
            'input' => 'text',
            'attribute_set' => 'Default',
            'backend' => '',
            'frontend' => '',
            'class' => '',
            'source' => '',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'default' => '',
            'filterable' => true,
            'searchable' => true,
            'comparable' => false,
            'unique' => false,
        ]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $group_id = $this->config->getAttributeGroupId($attributeSetId, 'General');
        $this->attributeManagement->assign(
            'catalog_product',
            $attributeSetId,
            $group_id,
            'product_instruction_pdf',
            130
        );
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}
