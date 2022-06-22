<?php

namespace Magenest\MobileApi\Setup\Patch\Data;

class SuperSaleHomePage extends \Magenest\MobileApi\Setup\Patch\AbstractAddCmsBlockPatch
{
    const SUPER_SALE_HOME_PAGE_MOBILE = 'super-sale-home-page-mobile';
    /**
     * Create cms block
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doUpdate()
    {
        // TODO: Implement doUpdate() method.
        $this->createCmsBlock(self::SUPER_SALE_HOME_PAGE_MOBILE, '[API HOME PAGE] SUPER SALE');
    }
}
