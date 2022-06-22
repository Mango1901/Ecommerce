<?php
namespace Magenest\QuantitySold\Setup\Patch\Data;

use Magenest\QuantitySold\Model\Product\Attribute\Backend\SoldQuantity;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddSoldQuantityAttribute implements DataPatchInterface
{
    const SOLD_QTY = "sold_qty";
    const FINAL_SOLD_QTY = "final_sold_qty";
    const UTILIZE_INITIAL_SOLD_QTY = "utilize_initial";

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
        $this->addSoldQuantityAttribute($eavSetup);
        $this->addFinalSoldQuantityAttribute($eavSetup);
        $this->addUtilizeInitialAttribute($eavSetup);
    }

    /**
     * Input attribute, not accounted initial qty
     *
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addSoldQuantityAttribute(EavSetup $eavSetup)
    {
        $config = [
            'type' => 'int',
            'label' => 'Sold Quantity',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
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
        if (!$eavSetup->getAttribute(Product::ENTITY, self::SOLD_QTY)) {
            $eavSetup->addAttribute(Product::ENTITY, self::SOLD_QTY, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, self::SOLD_QTY, $config);
        }
    }

    /**
     * Sortable attribute, used to display on FE
     *
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addFinalSoldQuantityAttribute(EavSetup $eavSetup)
    {
        $config = [
            'type' => 'int',
            'label' => 'Sold Quantity',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'backend' => SoldQuantity::class,
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 200,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'used_for_sort_by' => true,
            'default' => 0,
            'unique' => false
        ];
        if (!$eavSetup->getAttribute(Product::ENTITY, self::FINAL_SOLD_QTY)) {
            $eavSetup->addAttribute(Product::ENTITY, self::FINAL_SOLD_QTY, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, self::FINAL_SOLD_QTY, $config);
        }
    }

    /**
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addUtilizeInitialAttribute(EavSetup $eavSetup)
    {
        $config = [
            'type' => 'int',
            'label' => 'Utilize Initial Quantity Sold',
            'input' => 'select',
            'source' => Status::class,
            'sort_order' => 201,
            'required' => false,
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'searchable' => false,
            'default' => Status::STATUS_DISABLED,
            'used_in_product_listing' => false
        ];
        if (!$eavSetup->getAttribute(Product::ENTITY, self::UTILIZE_INITIAL_SOLD_QTY)) {
            $eavSetup->addAttribute(Product::ENTITY, self::UTILIZE_INITIAL_SOLD_QTY, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, self::UTILIZE_INITIAL_SOLD_QTY, $config);
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
