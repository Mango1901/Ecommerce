<?php

namespace Magenest\Promobar\Model\ResourceModel\Promobar;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init("Magenest\Promobar\Model\Promobar", "Magenest\Promobar\Model\ResourceModel\Promobar");
    }
}
