<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Model\ResourceModel;

class Label extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_menu_label', 'label_id');
    }
}
