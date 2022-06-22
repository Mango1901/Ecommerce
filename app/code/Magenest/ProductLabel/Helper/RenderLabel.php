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

namespace Magenest\ProductLabel\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class RenderLabel
 * @package Magenest\ProductLabel\Helper
 */
class RenderLabel extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DISPLAY_LABEL_ON_CARTPAGE = 'magenest_product_label/general/enable_label_on_cartpage';

    /**
     * @var \Magenest\ProductLabel\Model\Label\RenderLabel
     */
    private $renderLabel;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $product;

    /**
     * RenderLabel constructor.
     * @param Context $context
     * @param \Magenest\ProductLabel\Model\Label\RenderLabel $renderLabel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        \Magenest\ProductLabel\Model\Label\RenderLabel $renderLabel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->renderLabel = $renderLabel;
        $this->product = $productRepository;
        parent::__construct($context);
    }

    /**
     * @param $productId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function renderProductLabel($productId)
    {
        $isDisplayOnCartPage = $this->scopeConfig->isSetFlag(
            self::DISPLAY_LABEL_ON_CARTPAGE,
            ScopeInterface::SCOPE_STORE
        );
        $fullActionName = $this->_getRequest()->getFullActionName();
        if($fullActionName == 'checkout_cart_index' && !$isDisplayOnCartPage) {
            return '';
        }else {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->product->getById($productId);
            return $this->renderLabel->renderProductLabel($product, \Magenest\ProductLabel\Model\Label\RenderLabel::CATEGORY_PAGE);
        }
    }
}
