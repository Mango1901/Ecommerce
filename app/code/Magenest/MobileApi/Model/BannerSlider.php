<?php

namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Api\BannerSliderInterface;
use Magenest\MobileApi\Api\Data\SliderImageInterface;
use Magenest\MobileApi\Setup\Patch\Data\CustomerBannerSlider;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsCollection;
use Magenest\MobileApi\Api\Data\SliderImageInterfaceFactory as SliderImageFactory;

class BannerSlider implements BannerSliderInterface
{
    /** @var SliderImageFactory */
    protected $sliderImageFactory;

    /** @var CmsCollection */
    protected $blockCollectionFactory;

    /**
     * BannerSlider constructor.
     *
     * @param CmsCollection $blockCollectionFactory
     * @param SliderImageFactory $sliderImageFactory
     */
    function __construct(
        CmsCollection $blockCollectionFactory,
        SliderImageFactory $sliderImageFactory
    ) {
        $this->sliderImageFactory = $sliderImageFactory;
        $this->blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * Get banner slider in my account page
     *
     * @return SliderImageInterface
     */
    public function getBannerSlider() {
        $sliderImage = $this->sliderImageFactory->create();

        $block_banner_id = CustomerBannerSlider::CUSTOMER_BANNER_SLIDER_ID;

        $block_content_html = $this->blockCollectionFactory->create()
                                                           ->addFieldToFilter('identifier', $block_banner_id)
                                                           ->setCurPage(1)
                                                           ->setPageSize(1)
                                                           ->getFirstItem()
                                                           ->getContent();
        $regex = '/(\w+)*?wysiwyg(.[^}]+)/';
        $media_url = preg_match_all($regex, $block_content_html, $result);

        if ($media_url && isset($result[0])) {
            $sliderImage->setBannerSlider($result[0]);
        }

        return $sliderImage;
    }
}
