<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 06/12/2021
 * Time: 13:36
 */

namespace Magenest\CustomCatalog\Block\Product\View\Options\Type\Select;


use Magento\Catalog\Block\Product\View\Options\AbstractOptions;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Pricing\Price\CalculateCustomOptionCatalogRule;
use Magenest\CustomCatalog\Pricing\Price\CalculateCustomOptionCatalogRule as CalculateCustomOptionCatalogRuleMagenest;
use Magento\Catalog\Pricing\Price\CustomOptionPriceInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Html\Select;
use Magenest\CustomCatalog\Block\Product\View\Element\Html\TextSwatch as SwatchElement;
use Magento\Framework\View\Element\Template\Context;

class TextSwatch extends AbstractOptions
{
    /**
     * @var CalculateCustomOptionCatalogRuleMagenest
     */
    private $calculateCustomOptionCatalogRuleMagenest;

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * TextSwatch constructor.
     * @param Context $context
     * @param Data $pricingHelper
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param array $data
     * @param CalculateCustomOptionCatalogRule|null $calculateCustomOptionCatalogRule
     * @param CalculatorInterface|null $calculator
     * @param PriceCurrencyInterface|null $priceCurrency
     */
    public function __construct(
        Context $context,
        Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        CalculateCustomOptionCatalogRuleMagenest $calculateCustomOptionCatalogRuleMagenest,
        array $data = [],
        CalculateCustomOptionCatalogRule $calculateCustomOptionCatalogRule = null,
        CalculatorInterface $calculator = null,
        PriceCurrencyInterface $priceCurrency = null
    ) {
        $this->calculateCustomOptionCatalogRuleMagenest = $calculateCustomOptionCatalogRuleMagenest;
        $this->calculator = $calculator
            ?? ObjectManager::getInstance()->get(CalculatorInterface::class);
        $this->priceCurrency = $priceCurrency
            ?? ObjectManager::getInstance()->get(PriceCurrencyInterface::class);
        parent::__construct($context, $pricingHelper, $catalogData, $data, $calculateCustomOptionCatalogRule, $calculator, $priceCurrency);
    }

    /**
     * @inheritdoc
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $option = $this->getOption();
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId());
        $require = $option->getIsRequire() ? ' required' : '';
        $extraParams = '';
        /** @var SwatchElement $textSwatch */
        $textSwatch = $this->getLayout()->createBlock(
            SwatchElement::class
        )->setData(
            [
                'id' => 'select_' . $option->getId(),
                'class' => $require . ' product-custom-option admin__control-select'
            ]
        );
        $textSwatch->setName('options[' . $option->getId() . ']')->addOption('', __('-- Please Select --'));
        $textSwatch = $this->processSelectOption($textSwatch, $option);
        if (!$this->getSkipJsReloadPrice()) {
            $extraParams .= ' onchange="opConfig.reloadPrice()"';
        }
        $extraParams .= ' data-selector="' . $textSwatch->getName() . '"';
        $textSwatch->setExtraParams($extraParams);
        if ($configValue) {
            $textSwatch->setValue($configValue);
        }
        return $textSwatch->getHtml();
    }

    /**
     * Returns select with formated option prices
     *
     * @param SwatchElement $textSwatch
     * @param Option $option
     * @return SwatchElement
     */
    private function processSelectOption(SwatchElement $textSwatch, Option $option): SwatchElement
    {
        $store = $this->getProduct()->getStore();
        foreach ($option->getValues() as $_value) {
            $isPercentPriceType = $_value->getPriceType() === 'percent';
            $priceStr = $this->_formatPrice(
                [
                    'is_percent' => $isPercentPriceType,
                    'pricing_value' => $_value->getPrice($isPercentPriceType),
                    'apply_catalog_price_rule' => (bool)$_value->getApplyCatalogPriceRule()
                ],
                false
            );
            $textSwatch->addOption(
                $_value->getOptionTypeId(),
                $_value->getTitle() . ' (' . $priceStr . ')',
                [
                    'price' => $this->pricingHelper->currencyByStore(
                        $_value->getPrice(true),
                        $store,
                        false
                    )
                ]
            );
        }

        return $textSwatch;
    }

    /**
     * Return formatted price
     *
     * @param array $value
     * @param bool $flag
     * @return string
     */
    protected function _formatPrice($value, $flag = true)
    {
        if ($value['pricing_value'] == 0) {
            $sign = '';
        } else {
            $sign = '+';
        }

        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;

        $customOptionPrice = $this->getProduct()->getPriceInfo()->getPrice('custom_option_price');
        $isPercent = (bool) $value['is_percent'];
        $isApplyCatalogPriceRule = (bool) $value['apply_catalog_price_rule'];

        if (!$isPercent) {
            $catalogPriceValue = $this->calculateCustomOptionCatalogRuleMagenest->execute(
                $this->getProduct(),
                (float)$value['pricing_value'],
                $isPercent,
                $isApplyCatalogPriceRule
            );
            if ($catalogPriceValue !== null) {
                $value['pricing_value'] = $catalogPriceValue;
            }
        }

        $context = [CustomOptionPriceInterface::CONFIGURATION_OPTION_FLAG => true];
        $optionAmount = $isPercent
            ? $this->calculator->getAmount(
                $this->priceCurrency->roundPrice($value['pricing_value']),
                $this->getProduct(),
                null,
                $context
            ) : $customOptionPrice->getCustomAmount($value['pricing_value'], null, $context);
        $priceStr .= $this->getLayout()->getBlock('product.price.render.default')->renderAmount(
            $optionAmount,
            $customOptionPrice,
            $this->getProduct()
        );

        $priceStr = '<span class="price">' . $priceStr . '</span>';

        return $priceStr;
    }
}
