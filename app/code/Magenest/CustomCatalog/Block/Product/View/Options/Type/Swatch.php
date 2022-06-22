<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 06/12/2021
 * Time: 13:07
 */

namespace Magenest\CustomCatalog\Block\Product\View\Options\Type;


use Magenest\CustomCatalog\Block\Product\View\Options\Type\Select\TextSwatchFactory;
use Magento\Catalog\Block\Product\View\Options\AbstractOptions;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class Swatch extends AbstractOptions
{
    /**
     * @var TextSwatchFactory
     */
    private $textSwatchFactory;

    /**
     * Swatch constructor.
     * @param Context $context
     * @param Data $pricingHelper
     * @param CatalogHelper $catalogData
     * @param array $data
     * @param TextSwatchFactory|null $textSwatchFactory
     */
    public function __construct(
        Context $context,
        Data $pricingHelper,
        CatalogHelper $catalogData,
        array $data = [],
        TextSwatchFactory $textSwatchFactory = null
    ) {
        parent::__construct($context, $pricingHelper, $catalogData, $data);
        $this->textSwatchFactory = $textSwatchFactory ?: ObjectManager::getInstance()->get(TextSwatchFactory::class);
    }

    public function getValuesHtml(): string
    {
        $option = $this->getOption();
        $optionBlock = $this->textSwatchFactory->create();

        return $optionBlock
            ->setOption($option)
            ->setProduct($this->getProduct())
            ->setSkipJsReloadPrice(1)
            ->_toHtml();
    }
}
