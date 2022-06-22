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

namespace Magenest\CustomFrontend\Controller\Index;

class Add extends \Magento\Wishlist\Controller\Index\Add
{
    public function execute()
    {
        $wlItemId = $this->getItemInWishlist();
        if (!$wlItemId || empty($wlItemId)) {
            return parent::execute();
        }

        return $this->_forward('remove', 'index', 'wishlist', ['item' => $wlItemId]);
    }

    protected function getItemInWishlist()
    {
        $productId = $this->getRequest()->getParam('product', false);
        if ($productId) {
            $wishlist = $this->wishlistProvider->getWishlist();
            $wlItems = $wishlist->getItemCollection();
            foreach ($wlItems as $wlItem) {
                /** @var \Magento\Wishlist\Model\Item $wlItem */
                if ($productId == $wlItem->getProductId()) {
                    return $wlItem->getId();
                }
            }
        }

        return false;
    }
}
