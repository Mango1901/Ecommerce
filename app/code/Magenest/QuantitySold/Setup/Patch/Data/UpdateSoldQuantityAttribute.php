<?php
namespace Magenest\QuantitySold\Setup\Patch\Data;

use Magenest\QuantitySold\Model\Product\Attribute\Backend\TrueSoldQuantity;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateSoldQuantityAttribute implements DataPatchInterface
{
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
        $this->updateSoldQuantityAttribute($eavSetup);
    }

    /**
     * Input attribute, not accounted initial qty
     *
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function updateSoldQuantityAttribute(EavSetup $eavSetup)
    {
        $eavSetup->removeAttribute(Product::ENTITY, AddSoldQuantityAttribute::SOLD_QTY);
        $config = [
            'type' => 'int',
            'label' => 'Sold Quantity',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'backend' => TrueSoldQuantity::class,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 200,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => false,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'used_for_sort_by' => false,
            'default' => 0,
            'unique' => false
        ];
        $eavSetup->addAttribute(Product::ENTITY, AddSoldQuantityAttribute::SOLD_QTY, $config);
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [AddSoldQuantityAttribute::class];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
