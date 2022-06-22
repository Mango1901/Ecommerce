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

namespace Magenest\StoreCredit\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Ui\Component\Form\Field;

/**
 * Class StoreCredit
 * @package Magenest\StoreCredit\Ui\DataProvider\Product\Modifier
 */
class StoreCredit extends AbstractModifier
{
    /**
     * Store Credit Product attributes
     */
    const FIELD_MIN_CREDIT = 'min_credit';
    const FIELD_MAX_CREDIT = 'max_credit';
    const FIELD_CREDIT_RATE = 'credit_rate';
    const FIELD_CREDIT_AMOUNT = 'credit_amount';
    const FIELD_ALLOW_CREDIT_RANGE = 'allow_credit_range';

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @type array
     */
    protected $_meta;

    /**
     * StoreCredit constructor.
     *
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->_meta = $meta;

        $this->customizeAmountRange();

        return $this->_meta;
    }

    /**
     * customize allow amount range field
     *
     * @return $this|array
     */
    protected function customizeAmountRange()
    {
        $groupCode = $this->getGroupCodeByField($this->_meta, 'container_' . static::FIELD_ALLOW_CREDIT_RANGE);
        if (!$groupCode) {
            return $this;
        }

        // allow amount range field
        $containerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_ALLOW_CREDIT_RANGE,
            $this->_meta,
            null,
            'children'
        );
        $this->_meta = $this->arrayManager->merge($containerPath, $this->_meta, [
            'children' => [
                static::FIELD_ALLOW_CREDIT_RANGE => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataScope' => static::FIELD_ALLOW_CREDIT_RANGE,
                                'additionalClasses' => 'admin__field-x-small',
                                'component' => 'Magenest_StoreCredit/js/form/element/allow-amount-range',
                                'componentType' => Field::NAME,
                                'prefer' => 'toggle',
                                'valueMap' => [
                                    'false' => '0',
                                    'true' => '1',
                                ],
                                'exports' => [
                                    'checked' => '${$.parentName}.' . static::FIELD_ALLOW_CREDIT_RANGE . ':allowAmountRange',
                                    '__disableTmpl' => ['checked' => false],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // min amount & max amount field
        $minContainerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_MIN_CREDIT,
            $this->_meta,
            null,
            'children'
        );
        $maxContainerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_MAX_CREDIT,
            $this->_meta,
            null,
            'children'
        );
        $this->_meta = $this->arrayManager->merge($minContainerPath, $this->_meta, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magento_Ui/js/form/components/group',
                    ],
                ],
            ],
            'children' => [
                static::FIELD_MIN_CREDIT => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Range From'),
                                'validation' => ['validate-zero-or-greater' => true, 'required-entry' => true],
                                'additionalClasses' => 'admin__field-small',
                                'scopeLabel' => __('[WEBSITE]')
                            ],
                        ],
                    ],
                ]
            ]
        ]);
        $this->_meta = $this->arrayManager->merge($maxContainerPath, $this->_meta, [
            'children' => [
                static::FIELD_MAX_CREDIT => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('To'),
                                'validation' => ['validate-zero-or-greater' => true, 'required-entry' => true],
                                'additionalClasses' => 'admin__field-small admin__field-group-show-label'
                            ],
                        ],
                    ],
                ]
            ]
        ]);
        $this->_meta = $this->arrayManager->set(
            $minContainerPath . '/children/' . static::FIELD_MAX_CREDIT,
            $this->_meta,
            $this->arrayManager->get(
                $maxContainerPath . '/children/' . static::FIELD_MAX_CREDIT,
                $this->_meta
            )
        );
        $this->_meta = $this->arrayManager->remove($maxContainerPath, $this->_meta);

        // credit amount field
        $containerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_CREDIT_AMOUNT,
            $this->_meta,
            null,
            'children'
        );
        $this->_meta = $this->arrayManager->merge($containerPath, $this->_meta, [
            'children' => [
                static::FIELD_CREDIT_AMOUNT => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'addbefore' => $this->getStore()->getBaseCurrency()->getCurrencySymbol(),
                                'additionalClasses' => 'admin__field-small',
                                'validation' => ['validate-zero-or-greater' => true, 'required-entry' => true],
                                'service' => false,
                                'disabled' => false,
                                'globalScope' => true,
                                'scopeLabel' => __('[WEBSITE]')
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // price percentage field
        $containerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_CREDIT_RATE,
            $this->_meta,
            null,
            'children'
        );
        $this->_meta = $this->arrayManager->merge($containerPath, $this->_meta, [
            'children' => [
                static::FIELD_CREDIT_RATE => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'addbefore' => '%',
                                'additionalClasses' => 'admin__field-small',
                                'validation' => ['validate-number-range' => '0-100'],
                                'service' => false,
                                'disabled' => false,
                                'globalScope' => true,
                                'scopeLabel' => __('[WEBSITE]')
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return $this;
    }

    /**
     * Retrieve store
     *
     * @return StoreInterface
     */
    protected function getStore()
    {
        return $this->locator->getStore();
    }
}
