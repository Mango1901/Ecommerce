<?php


namespace Magenest\CustomSource\Model\ResourceModel\District;


use Magenest\Directory\Model\ResourceModel\District\Collection;

class DistrictCollection extends Collection
{
    public function toOptionArray()
    {
        return $this->prepareOptionArray();
    }
}
