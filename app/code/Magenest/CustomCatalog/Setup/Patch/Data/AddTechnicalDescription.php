<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 03/12/2021
 * Time: 10:27
 */

namespace Magenest\CustomCatalog\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;

/**
 * Class AddTechnicalDescription
 */
class AddTechnicalDescription implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * AddCustomSorting constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(Product::ENTITY, 'technical_description', [
            'type' => 'text',
            'label' => 'Technical Description',
            'input' => 'textarea',
            'sort_order' => 120,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'searchable' => true,
            'comparable' => true,
            'wysiwyg_enabled' => true,
            'is_html_allowed_on_front' => true,
            'visible_in_advanced_search' => true,
            'pagebuilder_enabled' => true,
            'group' => 'Content',
            'required' => false
        ]);

        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'technical_description',
            [
                'is_pagebuilder_enabled' => 1
            ]
        );
    }

}
