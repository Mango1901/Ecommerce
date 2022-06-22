<?php

namespace Magenest\MobileApi\Setup\Patch\Data;

class HuntSaleImmediatelyHomePage extends \Magenest\MobileApi\Setup\Patch\AbstractAddCmsBlockPatch
{
    const HUNT_SALE_IMMEDIATELY_HOME_PAGE_MOBILE = 'hunt-sale-immediately-home-page-mobile';
    /**
     * Create cms block
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doUpdate()
    {
        // TODO: Implement doUpdate() method.
        $this->createCmsBlock(self::HUNT_SALE_IMMEDIATELY_HOME_PAGE_MOBILE, '[API HOME PAGE] HUNT SALES');
    }
}
