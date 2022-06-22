<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 08/10/2020
 * Time: 19:06
 */

namespace Magenest\Core\Plugin;

class AddProductEntityId
{
    public function afterPrepareDocsPerStore($subject, $products, $documentsBatch, $scopeId)
    {
        foreach ($products as $key => &$product) {
            $product['product_id'] = $key;
        }
        return $products;
    }
}
