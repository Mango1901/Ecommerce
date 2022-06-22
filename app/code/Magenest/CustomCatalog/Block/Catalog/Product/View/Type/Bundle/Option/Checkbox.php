<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 08/12/2021
 * Time: 15:32
 */

namespace Magenest\CustomCatalog\Block\Catalog\Product\View\Type\Bundle\Option;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;

class Checkbox extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Checkbox
{
    /**
     * Get bundle option price title.
     *
     * @param Product $selection
     * @param bool $includeContainer
     * @return string
     */
    public function getSelectionQtyTitlePrice($selection, $includeContainer = true)
    {
        $this->setFormatProduct($selection);
        $priceTitle = '<span class="product-name">'
            . $selection->getSelectionQty() * 1
            . ' x '
            . $this->escapeHtml($selection->getName())
            . '</span><br>';
        $priceTitle .= "<div class='product-price-wrapper'>";
        $priceTitle .= $this->renderPrice($selection);
        $price = $this->getProduct()->getPriceInfo()->getPrice('bundle_option');
        $amount = $price->getOptionSelectionAmount($selection);
        if ($amount->getValue()) {
            $priceTitle .= ($includeContainer ? '<span class="price-notice">' : '') . '+' .
                $this->renderPriceString($selection, $includeContainer) . ($includeContainer ? '</span>' : '');
        }
        $priceTitle .= "</div>";
        return $priceTitle;
    }

    protected function renderPrice($selection)
    {
        /** @var \Magento\Bundle\Pricing\Price\BundleOptionPrice $price */
        $finalPrice = $selection->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE);
        $regularPrice = $selection->getPriceInfo()->getPrice(RegularPrice::PRICE_CODE);
        $renderer = $this->getLayout()->getBlock('product.price.render.default');
        $priceHtml = "";
        if ($finalPrice->getAmount() < $regularPrice->getAmount()) {
            $priceHtml .= "<span class=\"old-price\">" . $renderer->renderAmount(
                $regularPrice->getAmount(),
                $regularPrice,
                $selection,
                [
                        'include_container' => true
                    ]
            ) . "</span>";
        }

        $priceHtml .= "<span class=\"special-price\">" . $renderer->renderAmount(
            $finalPrice->getAmount(),
            $finalPrice,
            $selection,
            [
                    'include_container' => true
                ]
        ) . "</span>";

        return $priceHtml;
    }
}
