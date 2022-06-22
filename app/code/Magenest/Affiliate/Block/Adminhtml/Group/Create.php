<?php


namespace Magenest\Affiliate\Block\Adminhtml\Group;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

/**
 * Class Create
 * @package Magenest\Affiliate\Block\Adminhtml\Group
 */
class Create extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Create constructor.
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Group edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_Affiliate';
        $this->_controller = 'adminhtml_group';
        $this->_mode = 'create';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Group'));
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
    }

    /**
     * Retrieve text for header element depending on loaded Group
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('New Group');
    }
}
