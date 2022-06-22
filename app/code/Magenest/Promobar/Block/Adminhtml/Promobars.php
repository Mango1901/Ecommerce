<?php
namespace Magenest\Promobar\Block\Adminhtml;

class Promobars extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_promobars';
        $this->_blockGroup = 'Magenest_Promobar';
        $this->_headerText = __('Manage Bars');
        $this->_addButtonLabel = __('Add New Bar');
        parent::_construct();
    }

}
