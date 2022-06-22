<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Magenest\StoreCredit\Model\Product\Type\StoreCredit;

/**
 * Class InstallData
 * @package Magenest\StoreCredit\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var Product\AttributeSet\Options
     */
    protected $attributeSet;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param Product\AttributeSet\Options $attributeSet
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        Product\AttributeSet\Options $attributeSet,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->attributeSet = $attributeSet;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $quoteData = [
            ['table' => 'quote', 'column' => 'mp_store_credit_spent'],
            ['table' => 'quote', 'column' => 'mp_store_credit_discount'],
            ['table' => 'quote', 'column' => 'mp_store_credit_base_discount'],
        ];
        $salesData = [
            ['table' => 'order', 'column' => 'mp_store_credit_discount'],
            ['table' => 'order', 'column' => 'mp_store_credit_base_discount'],
            ['table' => 'invoice', 'column' => 'mp_store_credit_discount'],
            ['table' => 'invoice', 'column' => 'mp_store_credit_base_discount'],
            ['table' => 'creditmemo', 'column' => 'mp_store_credit_discount'],
            ['table' => 'creditmemo', 'column' => 'mp_store_credit_base_discount'],
        ];

        foreach ($quoteData as $item) {
            $quoteInstaller->addAttribute(
                $item['table'],
                $item['column'],
                ['type' => Table::TYPE_DECIMAL, 'visible' => false]
            );
        }
        foreach ($salesData as $item) {
            $salesInstaller->addAttribute(
                $item['table'],
                $item['column'],
                ['type' => Table::TYPE_DECIMAL, 'visible' => false]
            );
        }

        $quoteInstaller->addAttribute(
            'quote',
            'mp_store_credit_extra_content',
            ['type' => Table::TYPE_TEXT, 'visible' => false]
        );
        $salesInstaller->addAttribute(
            'order',
            'mp_store_credit_extra_content',
            ['type' => Table::TYPE_TEXT, 'visible' => false]
        );

        /** @var CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        /** Create product attribute group */
        $entityTypeId = $catalogSetup->getEntityTypeId(Category::ENTITY);

        foreach ($this->attributeSet->toOptionArray() as $set) {
            $catalogSetup->addAttributeGroup($entityTypeId, $set['value'], 'Store Credit Information', 10);
        }

        /** Add Product Attribute */
        $catalogSetup->addAttribute(Product::ENTITY, 'allow_credit_range', array_merge($this->getDefaultOptions(), [
            'label' => 'Allow Amount Range',
            'type' => 'int',
            'input' => 'select',
            'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 10
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'min_credit', array_merge($this->getDefaultOptions(), [
            'label' => 'Min Amount',
            'type' => 'decimal',
            'input' => 'price',
            'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
            'class' => 'validate-number',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 20
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'max_credit', array_merge($this->getDefaultOptions(), [
            'label' => 'Max Amount',
            'type' => 'decimal',
            'input' => 'price',
            'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
            'class' => 'validate-number',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 30
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'credit_amount', array_merge($this->getDefaultOptions(), [
            'label' => 'Credit Amount',
            'type' => 'decimal',
            'input' => 'price',
            'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
            'class' => 'validate-number',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 40
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'credit_rate', array_merge($this->getDefaultOptions(), [
            'label' => 'Price Percentage',
            'type' => 'decimal',
            'input' => 'price',
            'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
            'class' => 'validate-number',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 50
        ]));

        $this->updateApplyTo($catalogSetup);
    }

    /**
     * Update apply_to for store credit product attribute
     *
     * @param CategorySetup $catalogSetup
     *
     * @return $this
     */
    protected function updateApplyTo($catalogSetup)
    {
        $fieldAdd = ['tax_class_id'];
        foreach ($fieldAdd as $field) {
            $applyTo = $catalogSetup->getAttribute('catalog_product', $field, 'apply_to');
            if ($applyTo) {
                $applyTo = explode(',', $applyTo);
                if (!in_array(StoreCredit::TYPE_STORE_CREDIT, $applyTo)) {
                    $applyTo[] = StoreCredit::TYPE_STORE_CREDIT;
                    $catalogSetup->updateAttribute('catalog_product', 'weight', 'apply_to', join(',', $applyTo));
                }
            }
        }

        $fieldRemove = ['cost'];
        foreach ($fieldRemove as $field) {
            $applyTo = explode(',', $catalogSetup->getAttribute('catalog_product', $field, 'apply_to'));
            if (in_array(StoreCredit::TYPE_STORE_CREDIT, $applyTo)) {
                foreach ($applyTo as $k => $v) {
                    if ($v == StoreCredit::TYPE_STORE_CREDIT) {
                        unset($applyTo[$k]);
                        break;
                    }
                }
                $catalogSetup->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'group' => 'Store Credit Information',
            'backend' => '',
            'frontend' => '',
            'class' => '',
            'source' => '',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'unique' => false,
            'apply_to' => StoreCredit::TYPE_STORE_CREDIT,
            'used_in_product_listing' => true
        ];
    }
}
