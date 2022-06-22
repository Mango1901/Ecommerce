<?php

namespace Magenest\MobileApi\Setup\Patch\Data;

class BannerHomePage extends \Magenest\MobileApi\Setup\Patch\AbstractAddCmsBlockPatch
{
    const BANNER_HOME_PAGE_MOBILE = 'banner-home-page-mobile';
    /**
     * Create cms block
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doUpdate()
    {
        // TODO: Implement doUpdate() method.
        $this->createCmsBlock(self::BANNER_HOME_PAGE_MOBILE, '[API HOME PAGE] Banner');
    }
}
