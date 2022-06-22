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

namespace Magenest\MegaMenu\Model\ResourceModel\MegaMenu;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\MegaMenu\Model\ResourceModel\MegaMenu
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'menu_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Magenest\MegaMenu\Model\MegaMenu::class,
            \Magenest\MegaMenu\Model\ResourceModel\MegaMenu::class
        );
    }
}
