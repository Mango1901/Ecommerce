<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 23:37
 */

namespace Magenest\MapList\Controller\Adminhtml\Brand;

use Magenest\MapList\Controller\Adminhtml\Brand;

class NewAction extends Brand
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
