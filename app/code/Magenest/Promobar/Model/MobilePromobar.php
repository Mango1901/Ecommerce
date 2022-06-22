<?php
/**
 * Created by PhpStorm.
 * User: ducanh
 * Date: 19/02/2019
 * Time: 13:29
 */
namespace Magenest\Promobar\Model;
use Magento\Framework\Model\AbstractModel;

class MobilePromobar extends AbstractModel
{
    protected function _construct()
    {
        $this->_init("Magenest\Promobar\Model\ResourceModel\MobilePromobar");
    }
}