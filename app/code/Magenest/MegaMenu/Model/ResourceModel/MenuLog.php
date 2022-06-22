<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_SmartNet extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_SmartNet
 */

namespace Magenest\MegaMenu\Model\ResourceModel;

class MenuLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAGENEST_MEGA_MENU_LOG = 'magenest_mega_menu_log';

    protected $_idFieldName = 'log_id';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAGENEST_MEGA_MENU_LOG, 'log_id');
    }
}
