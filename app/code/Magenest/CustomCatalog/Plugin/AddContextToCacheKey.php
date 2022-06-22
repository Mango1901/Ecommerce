<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 10/06/2022
 * Time: 16:06
 */
declare(strict_types=1);

namespace Magenest\CustomCatalog\Plugin;

use Magenest\CustomSource\Helper\Data;

/**
 * Class AddContextToCacheKey
 */
class AddContextToCacheKey
{
    /**
     * @var Data
     */
    protected $sourceData;

    /**
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->sourceData = $data;
    }

    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterGetCacheKeyInfo($subject, $result)
    {
        $result['area_code'] = $this->sourceData->getCurrentArea();
        return $result;
    }
}
