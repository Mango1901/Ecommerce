<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 15/11/2021
 * Time: 09:09
 */

namespace Magenest\QuantitySold\Setup\Patch\Data;


use Magenest\QuantitySold\Model\Product\Attribute\Backend\SoldQuantity;
use Magenest\QuantitySold\Model\Product\Attribute\Backend\TrueSoldQuantity;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateToGlobalScope implements DataPatchInterface
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
        $this->updateUtilizeInitialAttribute($eavSetup);
        $this->updateFinalSoldQuantityAttribute($eavSetup);
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
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'backend' => TrueSoldQuantity::class,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 200,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'used_for_sort_by' => false,
            'default' => 0,
            'unique' => false
        ];
        $eavSetup->addAttribute(Product::ENTITY, AddSoldQuantityAttribute::SOLD_QTY, $config);
    }

    /**
     * Sortable attribute, used to display on FE
     *
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function updateFinalSoldQuantityAttribute(EavSetup $eavSetup)
    {
        $eavSetup->removeAttribute(Product::ENTITY, AddSoldQuantityAttribute::FINAL_SOLD_QTY);
        $config = [
            'type' => 'int',
            'label' => 'Sold Quantity',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'backend' => SoldQuantity::class,
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 200,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'used_for_sort_by' => true,
            'default' => 0,
            'unique' => false
        ];
        if (!$eavSetup->getAttribute(Product::ENTITY, AddSoldQuantityAttribute::FINAL_SOLD_QTY)) {
            $eavSetup->addAttribute(Product::ENTITY, AddSoldQuantityAttribute::FINAL_SOLD_QTY, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, AddSoldQuantityAttribute::FINAL_SOLD_QTY, $config);
        }
    }

    /**
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function updateUtilizeInitialAttribute(EavSetup $eavSetup)
    {
        $eavSetup->removeAttribute(Product::ENTITY, AddSoldQuantityAttribute::UTILIZE_INITIAL_SOLD_QTY);
        $config = [
            'type' => 'int',
            'label' => 'Utilize Initial Quantity Sold',
            'input' => 'select',
            'source' => Status::class,
            'sort_order' => 201,
            'required' => false,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'searchable' => false,
            'default' => Status::STATUS_DISABLED,
            'used_in_product_listing' => true
        ];
        if (!$eavSetup->getAttribute(Product::ENTITY, AddSoldQuantityAttribute::UTILIZE_INITIAL_SOLD_QTY)) {
            $eavSetup->addAttribute(Product::ENTITY, AddSoldQuantityAttribute::UTILIZE_INITIAL_SOLD_QTY, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, AddSoldQuantityAttribute::UTILIZE_INITIAL_SOLD_QTY, $config);
        }
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

