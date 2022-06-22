<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Model\ResourceModel\LabelCategory;

/**
 * Class Collection
 * @package Magenest\GiftRegistry\Model\ResourceModel\Event
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\ProductLabel\Model\LabelCategory', 'Magenest\ProductLabel\Model\ResourceModel\LabelCategory');
    }
}
