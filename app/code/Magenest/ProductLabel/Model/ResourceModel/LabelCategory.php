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

namespace Magenest\ProductLabel\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Event
 * @package Magenest\GiftRegistry\Model\ResourceModel
 */
class LabelCategory extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_product_label_option_category', 'option_id');
    }
}
