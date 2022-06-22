<?php
namespace Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeedIndex;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Magenest\FacebookShopping\Model\ResourceModel\FacebookFeedIndex
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init('Magenest\SellOnInstagram\Model\InstagramFeedIndex', 'Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeedIndex');
    }
}
