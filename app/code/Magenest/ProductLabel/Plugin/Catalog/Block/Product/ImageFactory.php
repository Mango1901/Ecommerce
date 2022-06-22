<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Plugin\Catalog\Block\Product;

class ImageFactory
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $metaData;

    /**
     * ImageFactory constructor.
     * @param \Magento\Framework\App\ProductMetadataInterface $metadata
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $metadata
    )
    {
        $this->metaData = $metadata;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ImageFactory $subject
     * @param $result
     * @return mixed
     */
    public function afterCreate(\Magento\Catalog\Block\Product\ImageFactory $subject, $result)
    {
        if ($this->metaData->getVersion() >= 2.4) {
            return $result->setTemplate('Magenest_ProductLabel::product/image_with_borders.phtml');
        } else {
            return $result->setTemplate('Magenest_ProductLabel::product/image_with_borders_ver2.3.phtml');
        }
    }
}
