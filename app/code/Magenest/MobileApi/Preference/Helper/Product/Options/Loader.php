<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Preference\Helper\Product\Options;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Api\Data\OptionInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Api\Data\OptionValueInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\ConfigurableProduct\Api\Data\OptionValueExtensionInterfaceFactory;
use Magento\Swatches\Helper\Data as SwatchData;

/**
 * Class Loader
 * @package Magenest\MobileApi\Preference\Helper\Product\Options
 */
class Loader extends \Magento\ConfigurableProduct\Helper\Product\Options\Loader
{
    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var SwatchData
     */
    private $swatchHelper;

    /**
     * ReadHandler constructor
     *
     * @param OptionValueInterfaceFactory $optionValueFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param OptionValueExtensionInterfaceFactory $optionValueExtensionInterfaceFactory
     * @param SwatchData $swatchData
     */
    public function __construct(
        OptionValueInterfaceFactory $optionValueFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        OptionValueExtensionInterfaceFactory $optionValueExtensionInterfaceFactory,
        SwatchData $swatchData
    )
    {
        parent::__construct($optionValueFactory, $extensionAttributesJoinProcessor);
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->swatchHelper = $swatchData;
    }

    /**
     * @inheritdoc
     */
    public function load(ProductInterface $product)
    {
        $options = [];
        /** @var Configurable $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $attributeCollection = $typeInstance->getConfigurableAttributeCollection($product);
        $this->extensionAttributesJoinProcessor->process($attributeCollection);

        foreach ($attributeCollection as $attribute) {
            $values = [];
            $attributeOptions = $attribute->getOptions();
            if (is_array($attributeOptions)) {
                $swatchesData = $this->swatchHelper->getSwatchesByOptionsId(array_column($attributeOptions, 'value_index'));
                foreach ($attributeOptions as $option) {
                    if(isset($swatchesData[$option['value_index']])){
                        $option['value'] = $swatchesData[$option['value_index']]['value'];
                        $option['type'] = $swatchesData[$option['value_index']]['type'];
                    }

                    $values[] = $option;
                }
            }

            $attribute->setValues($values);
            $options[] = $attribute;
        }

        return $options;
    }
}