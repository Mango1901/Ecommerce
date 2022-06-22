<?php
namespace Magenest\Promobar\Block\Adminhtml;

class Buttons extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_buttons';
        $this->_blockGroup = 'Magenest_Promobar';
        $this->_headerText = __('Manage Buttons');
        $this->_addButtonLabel = __('Add New Button');
        parent::_construct();
    }

}
