<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Store;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;
use Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface;
use Magenest\MobileApi\Api\Data\Store\HomeExtensionInterface;

/**
 * Interface HomeInterface
 * @package Magenest\MobileApi\Api\Data\Store
 */
interface HomeInterface extends ExtensibleDataInterface
{
    /**
     * Get licenses
     *
     * @return string
     * @since 102.0.0
     */
    public function getLicenses();

    /**
     * Set licenses
     *
     * @param string $licenses
     * @return $this
     */
    public function setLicenses($licenses);

    /**
     * Get token
     *
     * @return string
     * @since 102.0.0
     */
    public function getToken();

    /**
     * Set token
     *
     * @param string $token
     * @return $this
     */
    public function setToken($token);

    /**
     * Get categories
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getCategories();

    /**
     * Set categories
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[] $categories
     * @return $this
     */
    public function setCategories($categories);

    /**
     * Get config
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getConfig();

    /**
     * Set config
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[] $config
     * @return $this
     */
    public function setConfig($config);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magenest\MobileApi\Api\Data\Store\HomeExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magenest\MobileApi\Api\Data\Store\HomeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(HomeExtensionInterface $extensionAttributes);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     */
    public function getBannerSlider();

    /**
     * Set an extension attributes object.
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @return $this
     */
    public function setBannerSlider(array $bannerImages);
}
