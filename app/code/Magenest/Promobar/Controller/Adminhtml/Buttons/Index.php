<?php
namespace Magenest\Promobar\Controller\Adminhtml\Buttons;


class Index extends \Magenest\Promobar\Controller\Adminhtml\Buttons
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Buttons'));
        $this->_view->renderLayout();
    }
}
