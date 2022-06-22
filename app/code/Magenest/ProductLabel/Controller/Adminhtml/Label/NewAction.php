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

namespace Magenest\ProductLabel\Controller\Adminhtml\Label;

use Magenest\ProductLabel\Controller\Adminhtml\Label;

/**
 * Class NewAction
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class NewAction extends Label
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
