<?php

namespace Magenest\Promobar\Model;

use Magento\Framework\Model\AbstractModel;

class Promobar extends AbstractModel
{
    protected function _construct()
    {
        $this->_init("Magenest\Promobar\Model\ResourceModel\Promobar");
    }
}
