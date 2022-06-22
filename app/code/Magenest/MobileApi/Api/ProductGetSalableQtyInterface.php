<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 */

namespace Magenest\MobileApi\Api;

/**
 * Interface ProductGetSalableQty
 * @package Magenest\MobileApi\Api
 */
interface ProductGetSalableQtyInterface
{
    /**
     * @param string[] $ids
     *
     * @return array
     */
    public function getQty(array $ids);
}
