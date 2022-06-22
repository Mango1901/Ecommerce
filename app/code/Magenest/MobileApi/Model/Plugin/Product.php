<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Plugin;

/**
 * Class Product
 * @package Magenest\MobileApi\Model\Plugin
 */
class Product
{
    /**
     * After product Collection
     *
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return mixed
     */
    public function afterGetCrossSellProductCollection(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $result->addAttributeToSelect('*');
    }

    /**
     * After product Collection
     *
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterGetUpSellProductCollection($subject, $result)
    {
        return $result->addAttributeToSelect('*');
    }

    /**
     * After product Collection
     *
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterGetRelatedProductCollection($subject, $result)
    {
        return $result->addAttributeToSelect('*');
    }
}