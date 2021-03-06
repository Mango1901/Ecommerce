<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MapList\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Export controller
 */
abstract class Export extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magenest_MapList::export';
}
