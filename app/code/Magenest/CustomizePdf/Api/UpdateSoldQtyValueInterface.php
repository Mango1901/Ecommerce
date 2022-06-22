<?php

namespace Magenest\CustomizePdf\Api;

/**
 * Interface UpdateSoldQtyValueInterface
 */
interface UpdateSoldQtyValueInterface
{
    /**
     * @param $productId
     * @return array
     */
    public function execute($productId);
}
