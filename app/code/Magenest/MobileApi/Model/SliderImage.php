<?php
namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Api\Data\DataObjectInterface;
use Magenest\MobileApi\Api\Data\SliderImageInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class SliderImage extends AbstractExtensibleModel implements SliderImageInterface
{

    /**
     * Get banner image url
     *
     * @return mixed
     */
    public function getBannerSlider() {
        return $this->getData('banner_slider');
    }

    /**
     * Set banner image url
     *
     * @param DataObjectInterface[] $bannerSlider
     *
     * @return mixed
     */
    public function setBannerSlider( array $bannerSlider ) {
        return $this->setData('banner_slider', $bannerSlider);
    }
}
