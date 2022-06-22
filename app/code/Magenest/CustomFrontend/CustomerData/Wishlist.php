<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\CustomFrontend\CustomerData;

use Magenest\Core\Helper\CatalogHelper;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Wishlist\Block\Customer\Sidebar;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Wishlist extends \Magento\Wishlist\CustomerData\Wishlist
{
    protected $priceCurrency;

    public function __construct(
        Data $wishlistHelper,
        Sidebar $block,
        ImageFactory $imageHelperFactory,
        ViewInterface $view,
        PriceCurrencyInterface $priceCurrency,
        ItemResolverInterface $itemResolver = null
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct($wishlistHelper, $block, $imageHelperFactory, $view, $itemResolver);
    }

    protected function getItems()
    {
        $this->view->loadLayout();

        $collection = $this->wishlistHelper->getWishlistItemCollection();
        $collection->clear()->setInStockFilter(true)->setOrder('added_at');

        $items = [];
        foreach ($collection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }
        return $items;
    }

    /**
     * Retrieve wishlist item data
     *
     * @param Item $wishlistItem
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getItemData(Item $wishlistItem)
    {
        $product = $wishlistItem->getProduct();
        $result = parent::getItemData($wishlistItem);
        if ($discountPercent = $this->getDiscountFromWishList($product)) {
            $result['discount_percent'] = $discountPercent;
        } else {
            $result['discount_percent'] = false;
        }

        return $result;
    }

    /**
     * @param Product $product
     */
    private function getDiscountFromWishList($product)
    {
        $discountPercent = CatalogHelper::getDiscountPercent($product);

        return $discountPercent ? $discountPercent . '%' : null;
    }
}
