<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 23:06
 */

namespace Magenest\MapList\Model\ResourceModel\LocationGallery;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magenest\MapList\Helper\Constant;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Constant::LOCATION_GALLERY_MODEL, Constant::LOCATION_GALLERY_RESOURCE_MODEL);
    }
}
