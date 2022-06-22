<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Store;

use Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface;
use Magenest\MobileApi\Api\Data\Store\HomeInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magenest\MobileApi\Api\Data\Store\HomeExtensionInterface;

/**
 * Class Home
 * @package Magenest\MobileApi\Model\Store
 */
class Home extends AbstractExtensibleModel implements HomeInterface
{
    /** @const */
    const KEY_LICENSES = 'licenses';
    const KEY_TOKEN = 'token';
    const KEY_CATEGORIES = 'categories';
    const KEY_CONFIG = 'config';
    const KEY_BANNER = 'banner_slider';

    /**
     * {@inheritdoc}
     */
    public function getLicenses()
    {
        return $this->getData(self::KEY_LICENSES);
    }

    /**
     * {@inheritdoc}
     */
    public function setLicenses($licenses)
    {
        return $this->setData(self::KEY_LICENSES, $licenses);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->getData(self::KEY_TOKEN);
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        return $this->setData(self::KEY_TOKEN, $token);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories()
    {
        return $this->getData(self::KEY_CATEGORIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategories($categories)
    {
        return $this->setData(self::KEY_CATEGORIES, $categories);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->getData(self::KEY_CONFIG);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($config)
    {
        return $this->setData(self::KEY_CONFIG, $config);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(HomeExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @inheritdoc
     */
    public function getBannerSlider()
    {
        return $this->getData(self::KEY_BANNER);
    }

    /**
     * @inheritdoc
     */
    public function setBannerSlider($bannerImages)
    {
        return $this->setData(self::KEY_BANNER, $bannerImages);
    }
}
