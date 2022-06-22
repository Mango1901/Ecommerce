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

namespace Magenest\MegaMenu\Model;

use Magento\Framework\Model\AbstractModel;

class MenuLog extends AbstractModel
{
    protected $_idFieldName = 'log_id';

    protected $_eventPrefix = 'menulog';

    protected function _construct()
    {
        $this->_init(ResourceModel\MenuLog::class);
    }
}
