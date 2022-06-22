<?php
namespace Magenest\SellOnInstagram\Model;

use Magento\Framework\Model\AbstractModel;

class InstagramFeedIndex extends AbstractModel
{
    const TABLE = 'magenest_instagram_feed_index';

    protected $_eventPrefix = 'instagram_feed_index';

    public function _construct()
    {
        $this->_init(\Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeedIndex::class);
    }
}
