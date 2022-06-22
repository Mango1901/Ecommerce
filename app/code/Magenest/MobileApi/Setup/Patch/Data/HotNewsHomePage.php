<?php

namespace Magenest\MobileApi\Setup\Patch\Data;

class HotNewsHomePage extends \Magenest\MobileApi\Setup\Patch\AbstractAddCmsBlockPatch
{
    const HOT_NEWS_HOME_PAGE_MOBILE = 'hot-news-home-page-mobile';
    /**
     * Create cms block
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doUpdate()
    {
        $this->createCmsBlock(self::HOT_NEWS_HOME_PAGE_MOBILE, '[API HOME PAGE] HOT NEWS');
    }
}
