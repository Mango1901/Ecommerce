<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\Ward;

use Magenest\Directory\Controller\Adminhtml\Ward;

/**
 * Class NewAction
 * @package Magenest\Directory\Controller\Adminhtml\Ward
 */
class NewAction extends Ward
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
