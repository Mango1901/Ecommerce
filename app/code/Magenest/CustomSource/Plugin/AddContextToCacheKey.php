<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 24/11/2021
 * Time: 12:58
 */

namespace Magenest\CustomSource\Plugin;

use Magenest\CustomSource\Helper\Data;

class AddContextToCacheKey
{
    protected $sourceData;

    public function __construct(Data $data)
    {
        $this->sourceData = $data;
    }

    public function afterGetCacheKeyInfo($subject, $result)
    {
        $result['area_code'] = $this->sourceData->getCurrentArea();
        return $result;
    }
}
