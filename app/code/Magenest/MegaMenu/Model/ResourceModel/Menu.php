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

class Menu extends \Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb
{
    const MEGAMENU_TABLE = 'magenest_menu_entity';

    protected $_eventPrefix = 'menu_resource';

    protected $_eventObject = 'menu';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MEGAMENU_TABLE, 'entity_id');
    }
}
