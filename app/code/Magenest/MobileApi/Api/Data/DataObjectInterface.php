<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface;
use Magento\Framework\DataObject;
use Magenest\MobileApi\Model\Catalog\Widget\ProductSlider;

/**
 * Interface DataObjectInterface
 * @package Magenest\MobileApi\Api\Data
 */
interface DataObjectInterface extends ExtensibleDataInterface
{
    /**
     * Add data to the object.
     *
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr);

    /**
     * Overwrite data in the object.
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null);

    /**
     * Object data getter
     *
     ** @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function getData($key = '', $index = null);
}
