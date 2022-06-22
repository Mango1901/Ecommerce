<?php

namespace Magenest\CustomCatalog\Preference\Model\Product\Option;

use Magenest\CustomCatalog\Pricing\Price\CalculateCustomOptionCatalogRule as CalculateCustomOptionCatalogRuleMagenest;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory;
use Magento\Catalog\Pricing\Price\BasePrice;
use Magento\Catalog\Pricing\Price\CalculateCustomOptionCatalogRule;
use Magento\Catalog\Pricing\Price\CustomOptionPriceCalculator;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Value
 */
class Value extends \Magento\Catalog\Model\Product\Option\Value
{
    /**
     * @var CalculateCustomOptionCatalogRuleMagenest
     */
    private $calculateCustomOptionCatalogRuleMagenest;

    /**
     * @var CustomOptionPriceCalculator|null
     */
    private $customOptionPriceCalculator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $valueCollectionFactory
     * @param CalculateCustomOptionCatalogRuleMagenest $calculateCustomOptionCatalogRuleMagenest
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param CustomOptionPriceCalculator|null $customOptionPriceCalculator
     * @param CalculateCustomOptionCatalogRule|null $calculateCustomOptionCatalogRule
     */
    public function __construct(
        Context                                  $context,
        Registry                                 $registry,
        CollectionFactory                        $valueCollectionFactory,
        CalculateCustomOptionCatalogRuleMagenest $calculateCustomOptionCatalogRuleMagenest,
        AbstractResource                         $resource = null,
        AbstractDb                               $resourceCollection = null,
        array                                    $data = [],
        CustomOptionPriceCalculator              $customOptionPriceCalculator = null,
        CalculateCustomOptionCatalogRule         $calculateCustomOptionCatalogRule = null
    )
    {
        $this->calculateCustomOptionCatalogRuleMagenest = $calculateCustomOptionCatalogRuleMagenest;
        $this->_valueCollectionFactory = $valueCollectionFactory;
        $this->customOptionPriceCalculator = $customOptionPriceCalculator
            ?? ObjectManager::getInstance()->get(CustomOptionPriceCalculator::class);


        parent::__construct($context,
            $registry,
            $valueCollectionFactory,
            $resource,
            $resourceCollection,
            $data,
            $customOptionPriceCalculator,
            $calculateCustomOptionCatalogRule
        );
    }

    /**
     * Return price. If $flag is true and price is percent return converted percent to price
     *
     * @param bool $flag
     * @return float|int
     */
    public function getPrice($flag = false)
    {
        if ($flag) {
            $catalogPriceValue = $this->calculateCustomOptionCatalogRuleMagenest->execute(
                $this->getProduct(),
                (float)$this->getData(self::KEY_PRICE),
                $this->getPriceType() === self::TYPE_PERCENT,
                (bool)$this->getApplyCatalogPriceRule()
            );
            if ($catalogPriceValue !== null) {
                return $catalogPriceValue;
            } else {
                return $this->customOptionPriceCalculator->getOptionPriceByPriceCode($this, BasePrice::PRICE_CODE);
            }
        }
        return $this->_getData(self::KEY_PRICE);
    }
}
