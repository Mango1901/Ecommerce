<?php

namespace Magenest\Promobar\Model\ResourceModel\Button;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init("Magenest\Promobar\Model\Button", "Magenest\Promobar\Model\ResourceModel\Button");
    }
}
