<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\City;

use Magenest\Directory\Controller\Adminhtml\City;

/**
 * Class NewAction
 * @package Magenest\Directory\Controller\Adminhtml\City
 */
class NewAction extends City
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
