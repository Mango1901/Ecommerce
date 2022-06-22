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

namespace Magenest\ProductLabel\Plugin\Catalog\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Api\Data\ProductRenderExtensionFactory;

/**
 * Class Image
 * @package Magenest\ProductLabel\Plugin\Catalog\Ui\DataProvider\Product\Listing\Collector
 */
class Image
{
    /**
     * @var \Magenest\ProductLabel\Model\Label\RenderLabel
     */
    private $labelRender;

    /**
     * @var ProductRenderExtensionFactory
     */
    private $productRenderExtensionFactory;

    /**
     * Image constructor.
     * @param \Magenest\ProductLabel\Model\Label\RenderLabel $label
     * @param ProductRenderExtensionFactory $productRenderExtensionFactory
     */
    public function __construct(
        \Magenest\ProductLabel\Model\Label\RenderLabel $label,
        ProductRenderExtensionFactory $productRenderExtensionFactory
    ) {
        $this->labelRender = $label;
        $this->productRenderExtensionFactory = $productRenderExtensionFactory;
    }

    /**
     * @param \Magento\Catalog\Ui\DataProvider\Product\Listing\Collector\Image $subject
     * @param ProductInterface $product
     * @param ProductRenderInterface $productRender
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeCollect(
        \Magento\Catalog\Ui\DataProvider\Product\Listing\Collector\Image $subject,
        ProductInterface $product,
        ProductRenderInterface $productRender
    ) {
        $extensionAttributes = $productRender->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->productRenderExtensionFactory->create();
        }
        $labelHtml = $this->labelRender->renderProductLabel($product, \Magenest\ProductLabel\Model\Label\RenderLabel::CATEGORY_PAGE);
        $extensionAttributes->setProductLabelHtml($labelHtml);
        $productRender->setExtensionAttributes($extensionAttributes);

        return [$product, $productRender];
    }
}
