<?php

namespace Magenest\MobileApi\Setup\Patch\Data;

class MostWatchHomePage extends \Magenest\MobileApi\Setup\Patch\AbstractAddCmsBlockPatch
{
    const MOST_WATCH_HOME_PAGE_MOBILE = 'most-watch-home-page-mobile';
    /**
     * Create cms block
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doUpdate()
    {
        // TODO: Implement doUpdate() method.
        $this->createCmsBlock(self::MOST_WATCH_HOME_PAGE_MOBILE, '[API HOME PAGE] MOST WATCH');
    }
}
