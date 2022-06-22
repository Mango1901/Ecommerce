<?php

namespace Magenest\CustomCheckout\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class HashParam ResourceModel
 */
class HashParam extends AbstractDb
{
    /**
     * Function _construct
     */
    protected function _construct()
    {
        $this->_init('sales_guest_view', 'pk');
    }
}
