<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 18/11/2021
 * Time: 09:00
 */

namespace Magenest\FastErp\Setup\Patch\Data;

use Magento\Catalog\Model\Config;
use \Magenest\FastErp\Model\Product\Attribute\Backend\Dimension;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddDimensionAttribute implements DataPatchInterface
{
    const DIMENSION_ATTRIBUTES = [
        'length' => [
            'type' => 'decimal',
            'label' => 'Length',
            'input' => 'text',
            'backend' => Dimension::class,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 6,
            'apply_to' => 'simple,virtual',
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'visible' => true,
            'attribute_set' => 'Default',
            'filterable' => false,
            'searchable' => false,
            'comparable' => false,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'frontend_class' => 'validate-greater-than-zero validate-number',
        ],
        'width' => [
            'type' => 'decimal',
            'label' => 'Width',
            'input' => 'text',
            'backend' => Dimension::class,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 6,
            'apply_to' => 'simple,virtual',
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'visible' => true,
            'attribute_set' => 'Default',
            'filterable' => false,
            'searchable' => false,
            'comparable' => false,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'frontend_class' => 'validate-greater-than-zero validate-number',
        ],
        'height' => [
            'type' => 'decimal',
            'label' => 'Height',
            'input' => 'text',
            'backend' => Dimension::class,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 6,
            'apply_to' => 'simple,virtual',
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'visible' => true,
            'attribute_set' => 'Default',
            'filterable' => false,
            'searchable' => false,
            'comparable' => false,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'frontend_class' => 'validate-greater-than-zero validate-number',
        ],
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeManagementInterface
     */
    private $attributeManagement;

    /**
     * @var Config
     */
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

        foreach (self::DIMENSION_ATTRIBUTES as $code => $attr) {
            $eavSetup->addAttribute(Product::ENTITY, $code, $attr);
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
