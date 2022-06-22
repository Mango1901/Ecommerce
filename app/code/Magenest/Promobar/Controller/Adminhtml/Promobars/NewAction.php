<?php

namespace Magenest\Promobar\Controller\Adminhtml\Promobars;

class NewAction extends \Magenest\Promobar\Controller\Adminhtml\Promobars
{
    /**
     * Create new customer action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
}
