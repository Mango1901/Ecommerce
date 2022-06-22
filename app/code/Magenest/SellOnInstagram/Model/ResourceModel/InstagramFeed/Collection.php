<?php


namespace Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init('Magenest\SellOnInstagram\Model\InstagramFeed', 'Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed');
    }
}
