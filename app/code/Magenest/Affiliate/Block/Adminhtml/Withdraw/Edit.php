<?php


namespace Magenest\Affiliate\Block\Adminhtml\Withdraw;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Magenest\Affiliate\Model\Withdraw;

/**
 * Class Edit
 * @package Magenest\Affiliate\Block\Adminhtml\Withdraw
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
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
     * Initialize Withdraw edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'withdraw_id';
        $this->_blockGroup = 'Magenest_Affiliate';
        $this->_controller = 'adminhtml_withdraw';
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        if ($this->getRequest()->getParam('id')) {
            $this->buttonList->remove('save');
            $this->buttonList->remove('reset');
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded Withdraw
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Withdraw $withdraw */
        $withdraw = $this->_coreRegistry->registry('affiliate_withdraw_data');
        if ($withdraw->getId()) {
            return __("Edit Withdraw '%1'", $this->escapeHtml($withdraw->getId()));
        }

        return __('New Withdraw');
    }
}
