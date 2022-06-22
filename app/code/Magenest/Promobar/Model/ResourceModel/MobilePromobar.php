<?php
/**
 * Created by PhpStorm.
 * User: ducanh
 * Date: 19/02/2019
 * Time: 13:30
 */
namespace Magenest\Promobar\Model\ResourceModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\DateTime;

class MobilePromobar extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_mobile_promobar', 'mobile_promobar_id');
    }

}