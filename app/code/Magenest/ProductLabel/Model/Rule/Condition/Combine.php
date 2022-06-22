<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Model\Rule\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magenest\ProductLabel\Model\Rule\Condition\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * Combine constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param ProductFactory $conditionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magenest\ProductLabel\Model\Rule\Condition\ProductFactory $conditionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Swatches\Helper\Data $swatchHelper,
        array $data = []
    ) {
        $this->productFactory = $conditionFactory;
        $this->registry = $registry;
        $this->moduleManager = $moduleManager;
        $this->eavConfig = $eavConfig;
        $this->swatchHelper = $swatchHelper;
        parent::__construct($context, $data);
        $this->setType(\Magenest\ProductLabel\Model\Rule\Condition\Combine::class);
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions, [
            [
                'value' => \Magenest\ProductLabel\Model\Rule\Condition\Combine::class,
                'label' => __('Conditions Combination'),
            ]
        ]);

        $productAttributes = $this->productFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($productAttributes as $code => $label) {
            $attributeSwatch = $this->eavConfig->getAttribute('catalog_product', $code);
            //Check attribute swatch option
            $attributesCode =  $this->swatchHelper->isSwatchAttribute($attributeSwatch);
            if ($attributesCode != true) {
                $attributes[] = [
                    'value' => 'Magenest\ProductLabel\Model\Rule\Condition\Product|' . $code,
                    'label' => $label,
                ];
            }
        }

        $conditions = array_merge_recursive(
            $conditions, [
            [
                'label' => __('Product Attribute'),
                'value' => $attributes
            ]
        ]);

        return $conditions;
    }

    /**
     * @param $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
