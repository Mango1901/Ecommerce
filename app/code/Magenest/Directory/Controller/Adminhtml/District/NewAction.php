<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\District;

use Magenest\Directory\Controller\Adminhtml\District;

/**
 * Class NewAction
 * @package Magenest\Directory\Controller\Adminhtml\District
 */
class NewAction extends District
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
