<?php

namespace Magenest\MobileApi\Setup\Patch\Data;

class BestSellerHomePage extends \Magenest\MobileApi\Setup\Patch\AbstractAddCmsBlockPatch
{
    const BEST_SELLER_HOME_PAGE_MOBILE = 'best-seller-home-page-mobile';
    /**
     * Create cms block
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doUpdate()
    {
        // TODO: Implement doUpdate() method.
        $this->createCmsBlock(self::BEST_SELLER_HOME_PAGE_MOBILE, '[API HOME PAGE] BEST SELLER');
    }
}
