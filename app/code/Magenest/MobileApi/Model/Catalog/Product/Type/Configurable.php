<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Locale\Format;
use Magento\Framework\App\ObjectManager;
use Magento\ConfigurableProduct\Helper\Data as ConfigurableHelper;
use Magenest\MobileApi\Model\Catalog\Product\Configurable\ConfigFactory as ApiConfigurableConfigFactory;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magenest\MobileApi\Model\Helper;

/**
 * Class Configurable
 * @package Magenest\MobileApi\Model\Catalog\Product\Configurable
 */
class Configurable implements OptionsInterface
{
    /**
     * @var Format
     */
    protected $_localeFormat;

    /**
     * @var ConfigurableHelper
     */
    protected $_configurableHelper;

    /**
     * @var ApiConfigurableConfigFactory
     */
    protected $_apiConfigurableConfigFactory;

    /**
     * @var Emulation
     */
    protected $_appEmulation;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Helper
     */
    protected $_apiHelper;

    /**
     * Constructor.
     *
     * @param ApiConfigurableConfigFactory $apiConfigurableConfigFactory
     * @param ConfigurableHelper $configurableHelper
     * @param Emulation $appEmulation
     * @param StoreManagerInterface $storeManager
     * @param Helper $apiHelper
     * @param Format|null $localeFormat
     */
    function __construct(
        ApiConfigurableConfigFactory $apiConfigurableConfigFactory,
        ConfigurableHelper $configurableHelper,
        Emulation $appEmulation,
        StoreManagerInterface $storeManager,
        Helper $apiHelper,
        Format $localeFormat = null
    )
    {
        $this->_apiConfigurableConfigFactory = $apiConfigurableConfigFactory;
        $this->_configurableHelper = $configurableHelper;
        $this->_appEmulation = $appEmulation;
        $this->_storeManager = $storeManager;
        $this->_apiHelper = $apiHelper;
        $this->_localeFormat = $localeFormat ?: ObjectManager::getInstance()->get(Format::class);
    }

    /**
     * @inheritdoc
     */
    public function process(ProductInterface $product)
    {
        $childrenProducts = $product->getTypeInstance()->getUsedProducts($product, null);
        $allowAttributes = $product->getTypeInstance()->getConfigurableAttributes($product);
        $optionConfig = [];

        if ($this->_apiHelper->isDetailRequest()) {
            $this->_appEmulation->startEnvironmentEmulation($this->_storeManager->getStore()->getId(), Area::AREA_FRONTEND, true);
            /** @var \Magento\Catalog\Model\Product $child */
            foreach ($childrenProducts as $child) {
                if ($child->isSaleable()) {
                    $productImages = $this->_configurableHelper->getGalleryImages($child) ?: [];
                    $priceInfo = $child->getPriceInfo();
                    $optionConfig['option_prices'][$child->getId()] = [
                        'basePrice' => $this->_localeFormat->getNumber($priceInfo->getPrice('regular_price')->getAmount()->getBaseAmount()),
                        'finalPrice' => $this->_localeFormat->getNumber($priceInfo->getPrice('final_price')->getAmount()->getValue())
                    ];

                    foreach ($allowAttributes as $attribute) {
                        $productAttribute = $attribute->getProductAttribute();
                        $productAttributeId = $productAttribute->getId();
                        $attributeValue = $child->getData($productAttribute->getAttributeCode());

                        $optionConfig['index'][$child->getId()][$productAttributeId] = $attributeValue;
                    }

                    foreach ($productImages as $image) {
                        $optionConfig['images'][$child->getId()][] = [
                            'thumb' => $image->getData('small_image_url'),
                            'img' => $image->getData('medium_image_url'),
                            'full' => $image->getData('large_image_url'),
                            'caption' => $image->getLabel(),
                            'position' => $image->getPosition(),
                            'isMain' => $image->getFile() == $child->getImage(),
                            'type' => str_replace('external-', '', $image->getMediaType()),
                            'videoUrl' => $image->getVideoUrl(),
                        ];
                    }
                }
            }

            $configurableConfig = $this->_apiConfigurableConfigFactory->create()
                ->setIndex(['result' => $optionConfig['index'] ?? []])
                ->setOptionPrices(isset($optionConfig['option_prices']) ? ['result' => $optionConfig['option_prices']] : [])
                ->setImages(isset($optionConfig['images']) ? ['result' => $optionConfig['images']] : []);

            $extensionAttributes = $product->getExtensionAttributes();
            $extensionAttributes->setConfigurableConfig($configurableConfig);
            $product->setExtensionAttributes($extensionAttributes);
            $this->_appEmulation->stopEnvironmentEmulation();
        }else {
            /** @var \Magento\Catalog\Model\Product $child */
            foreach ($childrenProducts as $child) {
                if ($child->isSaleable()) {
                    $priceInfo = $child->getPriceInfo();
                    $product->setPrice($this->_localeFormat->getNumber($priceInfo->getPrice('final_price')->getAmount()->getValue()));
                    $product->setPriceCalculation(false);
                    break;
                }
            }
        }
    }
}
