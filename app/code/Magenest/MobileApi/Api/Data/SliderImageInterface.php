<?php
namespace Magenest\MobileApi\Api\Data;

use Magenest\MobileApi\Api\Data\DataObjectInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SliderImageInterface
 * @package Magenest\MobileApi\Api
 */
interface SliderImageInterface extends ExtensibleDataInterface
{
    /**
     * Get banner slider image
     *
     * @return DataObjectInterface[]
     */
    public function getBannerSlider();

    /**
     * Get banner slider image
     *
     * @param DataObjectInterface[] $bannerSlider
     *
     * @return $this
     */
    public function setBannerSlider( array $bannerSlider);
}
