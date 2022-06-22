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

namespace Magenest\ProductLabel\Model\ResourceModel\Label;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\ProductLabel\Model\ResourceModel\Label
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'label_id';

    protected function _construct()
    {
        $this->_init(\Magenest\ProductLabel\Model\Label::class, \Magenest\ProductLabel\Model\ResourceModel\Label::class);
    }
}
