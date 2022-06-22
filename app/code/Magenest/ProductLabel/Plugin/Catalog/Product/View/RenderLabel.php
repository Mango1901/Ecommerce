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

namespace Magenest\ProductLabel\Plugin\Catalog\Product\View;

class RenderLabel
{

    /**
     * @var \Magenest\ProductLabel\Model\Label\RenderLabel
     */
    private $labelRender;

    /**
     * RenderLabel constructor.
     * @param \Magenest\ProductLabel\Model\Label\RenderLabel $label
     */
    public function __construct(
        \Magenest\ProductLabel\Model\Label\RenderLabel $label
    ) {
        $this->labelRender = $label;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\Gallery $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        \Magento\Catalog\Block\Product\View\Gallery $subject,
        $result
    ) {
        $product = $subject->getProduct();
        $layoutName = $subject->getNameInLayout();
        if ($product && $layoutName == 'product.info.media.image') {
            $result .= $this->labelRender->renderProductLabel($product, \Magenest\ProductLabel\Model\Label\RenderLabel::PRODUCT_PAGE);
        }

        return $result;
    }
}
