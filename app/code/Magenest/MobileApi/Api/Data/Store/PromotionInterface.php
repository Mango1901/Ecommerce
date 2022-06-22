<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Store;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;
use Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface;

/**
 * Interface PromotionInterface
 * @package Magenest\MobileApi\Api\Data\Store
 */
interface PromotionInterface extends ExtensibleDataInterface
{
    /**
     * Get banner
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @since 102.0.0
     */
    public function getBanner();

    /**
     * Set banner
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface $banner
     * @return $this
     */
    public function setBanner($banner);

    /**
     * Get panels
     *
     * @return \Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface[]
     * @since 102.0.0
     */
    public function getPanels();

    /**
     * Set panels
     *
     * @param \Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface[] $panels
     * @return $this
     */
    public function setPanels($panels);
}
