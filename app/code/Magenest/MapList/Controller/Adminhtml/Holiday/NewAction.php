<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 23:37
 */

namespace Magenest\MapList\Controller\Adminhtml\Holiday;

use Magenest\MapList\Controller\Adminhtml\Holiday;

class NewAction extends Holiday
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
