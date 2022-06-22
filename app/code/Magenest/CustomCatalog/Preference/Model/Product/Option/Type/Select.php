<?php

namespace Magenest\CustomCatalog\Preference\Model\Product\Option\Type;

use Magenest\CustomCatalog\Pricing\Price\CalculateCustomOptionCatalogRule as CalculateCustomOptionCatalogRuleAliasMagenst;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Catalog\Pricing\Price\CalculateCustomOptionCatalogRule;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\StringUtils;

class Select extends Option\Type\Select
{
    /**
     * @var CalculateCustomOptionCatalogRuleAliasMagenst
     */
    private $calculateCustomOptionCatalogRuleMagenest;

    /**
     * @param Session $checkoutSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StringUtils $string
     * @param Escaper $escaper
     * @param CalculateCustomOptionCatalogRuleAliasMagenst $calculateCustomOptionCatalogRuleMagenest
     * @param array $data
     * @param array $singleSelectionTypes
     * @param CalculateCustomOptionCatalogRule|null $calculateCustomOptionCatalogRule
     */
    public function __construct(
        Session                                      $checkoutSession,
        ScopeConfigInterface                         $scopeConfig,
        StringUtils                                  $string,
        Escaper                                      $escaper,
        CalculateCustomOptionCatalogRuleAliasMagenst $calculateCustomOptionCatalogRuleMagenest,
        array                                        $data = [],
        array                                        $singleSelectionTypes = [],
        CalculateCustomOptionCatalogRule             $calculateCustomOptionCatalogRule = null
    ) {
        $this->calculateCustomOptionCatalogRuleMagenest = $calculateCustomOptionCatalogRuleMagenest;
        parent::__construct(
            $checkoutSession,
            $scopeConfig,
            $string,
            $escaper,
            $data,
            $singleSelectionTypes,
            $calculateCustomOptionCatalogRule
        );
    }

    /**
     * Return Price for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param float $basePrice
     * @return float
     */
    public function getOptionPrice($optionValue, $basePrice)
    {
        $option = $this->getOption();
        $result = 0;

        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $value) {
                $_result = $option->getValueById($value);
                if ($_result) {
                    $result += $this->getCalculatedOptionValue($option, $_result, $basePrice);
                } else {
                    if ($this->getListener()) {
                        $this->getListener()->setHasError(true)->setMessage($this->_getWrongConfigurationMessage());
                        break;
                    }
                }
            }
        } elseif ($this->_isSingleSelection()) {
            $_result = $option->getValueById($optionValue);
            if ($_result) {
                $catalogPriceValue = $this->calculateCustomOptionCatalogRuleMagenest->execute(
                    $option->getProduct(),
                    (float)$_result->getPrice(),
                    $_result->getPriceType() === Value::TYPE_PERCENT,
                    (bool)$_result->getApplyCatalogPriceRule()
                );
                if ($catalogPriceValue !== null) {
                    $result = $catalogPriceValue;
                } else {
                    $result = $this->_getChargeableOptionPrice(
                        $_result->getPrice(),
                        $_result->getPriceType() == 'percent',
                        $basePrice
                    );
                }
            } else {
                if ($this->getListener()) {
                    $this->getListener()->setHasError(true)->setMessage($this->_getWrongConfigurationMessage());
                }
            }
        }

        return $result;
    }

    /**
     * Returns calculated price of option
     *
     * @param Option $option
     * @param Option\Value $result
     * @param float $basePrice
     * @return float
     */
    protected function getCalculatedOptionValue(Option $option, Value $result, float $basePrice): float
    {
        $catalogPriceValue = $this->calculateCustomOptionCatalogRuleMagenest->execute(
            $option->getProduct(),
            (float)$result->getPrice(),
            $result->getPriceType() === Value::TYPE_PERCENT,
            (bool)$result->getApplyCatalogPriceRule()
        );
        if ($catalogPriceValue !== null) {
            $optionCalculatedValue = $catalogPriceValue;
        } else {
            $optionCalculatedValue = $this->_getChargeableOptionPrice(
                $result->getPrice(),
                $result->getPriceType() === Value::TYPE_PERCENT,
                $basePrice
            );
        }
        return $optionCalculatedValue;
    }
}
