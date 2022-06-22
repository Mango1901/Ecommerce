<?php

namespace Magenest\MobileApi\Setup\Patch\Data;

class SliderHomePage extends \Magenest\MobileApi\Setup\Patch\AbstractAddCmsBlockPatch
{
    const SLIDER_HOME_PAGE_MOBILE = 'slider-home-page-mobile';
    /**
     * Create cms block
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doUpdate()
    {
        // TODO: Implement doUpdate() method.
        $this->createCmsBlock(self::SLIDER_HOME_PAGE_MOBILE, '[API HOME PAGE] SLIDER');
    }
}
