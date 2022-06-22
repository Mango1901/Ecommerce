<?php
namespace Magenest\Promobar\Controller\Adminhtml\Promobars;


class Index extends \Magenest\Promobar\Controller\Adminhtml\Promobars
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Bars'));
        $this->_view->renderLayout();
    }
}
