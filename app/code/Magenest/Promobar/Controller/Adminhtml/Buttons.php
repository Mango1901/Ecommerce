<?php
namespace Magenest\Promobar\Controller\Adminhtml;
abstract class Buttons extends \Magento\Backend\App\Action
{
    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magenest_Promobar::promobar_manage'
        )->_addBreadcrumb(
            __('Buttons'),
            __('Buttons')
        );
        return $this;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Promobar::manage_button');
    }
}
