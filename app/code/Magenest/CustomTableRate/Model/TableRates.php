<?php

namespace Magenest\CustomTableRate\Model;

use Magento\Framework\Model\AbstractModel;

class TableRates extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Magenest\CustomTableRate\Model\ResourceModel\Carrier::class);
    }
}
