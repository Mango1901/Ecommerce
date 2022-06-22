<?php

namespace Magenest\CustomCatalog\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $_eavSetupFactory;
    protected $eavConfig;
    protected $categorySetupFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $config
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $config,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->_eavSetupFactory     = $eavSetupFactory;
        $this->eavConfig            = $config;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Upgrade method
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addAttributeProduct($setup);
        }
        $setup->endSetup();
    }

    private function addAttributeProduct($setup)
    {
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'price_list_release_date',
            [
                'type' => 'datetime',
                'label' => 'Price List Release Date',
                'input' => 'date',
                'class' => 'validate-date',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'filterable_in_search' => false,
                'required' => false,
                'system' => true,
                'user_defined' => false,
                'visible_on_front' => false,
                'group' => 'General',
                'sort_order' => 41,
                'apply_to' => '',
            ]
        );
    }
}
