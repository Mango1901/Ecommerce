<?php
namespace Magenest\MobileApi\Api;

use Magenest\MobileApi\Api\Data\SliderImageInterface;

/**
 * Interface BannerSliderInterface
 * @package Magenest\MobileApi\Api
 */
interface BannerSliderInterface
{
    /**
     * Get banner slider image
     *
     * @return SliderImageInterface
     */
    public function getBannerSlider();
}
