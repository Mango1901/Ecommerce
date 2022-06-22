<?php
/**
 * Created by PhpStorm.
 * User: ducanh
 * Date: 19/02/2019
 * Time: 13:31
 */
namespace Magenest\Promobar\Model\ResourceModel\MobilePromobar;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init("Magenest\Promobar\Model\MobilePromobar", "Magenest\Promobar\Model\ResourceModel\MobilePromobar");
    }
}