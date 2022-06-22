<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 06/12/2021
 * Time: 14:40
 */

namespace Magenest\CustomCatalog\Plugin;

class SwatchOption
{

    /**
     * @param \Magento\Catalog\Model\Product\Option $subject
     * @param $result
     * @param string|null $type
     * @return bool
     */
    public function afterHasValues(\Magento\Catalog\Model\Product\Option $subject, $result, $type = null)
    {
        if (!$result) {
            return $subject->getGroupByType($type) == 'swatch';
        }
        return $result;
    }
}
