<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 06/12/2021
 * Time: 13:17
 */

namespace Magenest\CustomCatalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magenest\CustomCatalog\Model\Config\Source\YesNo;

class CustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions
{
    /**#@+
     * Field values
     */
    const FIELD_APPLY_CATALOG_PRICE_RULE = 'apply_catalog_price_rule';
    /**#@-*/

    /**
     * @var YesNo
     */
    private $yesNoOptions;

    /**
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $productOptionsConfig
     * @param ProductOptionsPrice $productOptionsPrice
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param YesNo $yesNoOptions
     */
    public function __construct(
        LocatorInterface      $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface       $productOptionsConfig,
        ProductOptionsPrice   $productOptionsPrice,
        UrlInterface          $urlBuilder,
        ArrayManager          $arrayManager,
        YesNo                 $yesNoOptions
    ) {
        $this->yesNoOptions = $yesNoOptions;
        parent::__construct(
            $locator,
            $storeManager,
            $productOptionsConfig,
            $productOptionsPrice,
            $urlBuilder,
            $arrayManager
        );
    }

    /**
     * @inheritDoc
     */
    protected function getTypeFieldConfig($sortOrder)
    {
        $data = parent::getTypeFieldConfig($sortOrder);
        return array_merge_recursive($data, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'groupsConfig' => [
                            'swatch' => [
                                'values' => ['text_swatch'],
                                'indexes' => [
                                    static::GRID_TYPE_SELECT_NAME
                                ]
                            ],
                            'text' => [
                                'indexes' => [
                                    static::FIELD_APPLY_CATALOG_PRICE_RULE
                                ]
                            ],
                            'file' => [
                                'indexes' => [
                                    static::FIELD_APPLY_CATALOG_PRICE_RULE
                                ]
                            ],
                            'select' => [
                                'indexes' => [
                                    static::FIELD_APPLY_CATALOG_PRICE_RULE
                                ]
                            ],
                            'data' => [
                                'indexes' => [
                                    static::FIELD_APPLY_CATALOG_PRICE_RULE
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getSelectTypeGridConfig($sortOrder)
    {
        $data = parent::getSelectTypeGridConfig($sortOrder);

        return array_merge_recursive($data, [
            'children' => [
                'record' => [
                    'children' => [
                        static::FIELD_APPLY_CATALOG_PRICE_RULE => $this->getApplyPriceRuleFieldConfig(35, ['fit' => true]),
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getStaticTypeContainerConfig($sortOrder)
    {
        $data = parent::getStaticTypeContainerConfig($sortOrder);

        return array_merge_recursive($data, [
            'children' => [
                static::FIELD_APPLY_CATALOG_PRICE_RULE => $this->getApplyPriceRuleFieldConfig(25, ['fit' => true]),
            ],
        ]);
    }

    /**
     * Get config for "	Apply Catalog Price Rule" field
     *
     * @param int $sortOrder
     * @param array $config
     * @return array
     * @since 101.0.0
     */
    protected function getApplyPriceRuleFieldConfig($sortOrder, array $config = [])
    {
        return array_replace_recursive(
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Apply Catalog Price Rule'),
                            'component' => 'Magento_Ui/js/form/element/select',
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'dataScope' => static::FIELD_APPLY_CATALOG_PRICE_RULE,
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                            'options' => $this->yesNoOptions->toOptionArray(),
                        ],
                    ],
                ],
            ],
            $config
        );
    }
}
