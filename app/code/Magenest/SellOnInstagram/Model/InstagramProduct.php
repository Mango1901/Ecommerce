<?php

namespace Magenest\SellOnInstagram\Model;

use Magento\Framework\Model\AbstractModel;


class InstagramProduct extends AbstractModel
{
    const TABLE = 'magenest_instagram_feed_product';
    const STATUS_COLUMN = 'status';

    public function _construct()
    {
        $this->_init("Magenest\SellOnInstagram\Model\ResourceModel\InstagramProduct");
    }
}
