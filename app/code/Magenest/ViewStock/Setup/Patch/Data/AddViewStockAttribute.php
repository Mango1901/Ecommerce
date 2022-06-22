<?php
namespace Magenest\ViewStock\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddViewStockAttribute implements DataPatchInterface
{
    const VIEW_STOCK = "view_stock";

    /** @var ModuleDataSetupInterface */
    private $setup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $this->addViewStockAttribute($eavSetup);
    }

    /**
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addViewStockAttribute(EavSetup $eavSetup)
    {
        $config = [
            'type' => 'int',
            'label' => 'Display Salable Quantity on Frontend',
            'input' => 'select',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'source' => Status::class,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 250,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => true,
            'default' => Status::STATUS_DISABLED,
            'unique' => false
        ];
        if (!$eavSetup->getAttribute(Product::ENTITY, self::VIEW_STOCK)) {
            $eavSetup->addAttribute(Product::ENTITY, self::VIEW_STOCK, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, self::VIEW_STOCK, $config);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
