<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Transaction;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Magenest\RewardPoints\Block\Adminhtml\Transaction
 */
class Edit extends FormContainer
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Registry $registry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        array $data
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Page Constructor
     */
    protected function _construct()
    {
        $templates = $this->_coreRegistry->registry('rewardpoints_transaction');

        $this->_objectId   = 'id';
        $this->_blockGroup = 'Magenest_RewardPoints';
        $this->_controller = 'adminhtml_transaction';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Add Transaction'));
        $this->buttonList->update('delete', 'label', __('Delete Transaction'));
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $templates = $this->_coreRegistry->registry('rewardpoints_transaction');
        if ($templates->getId()) {
            return __("Edit Transaction '%1'", $this->escapeHtml($templates->getTitle()));
        }

        return __('New Transaction');
    }

    /**
     * @return string
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'rewardpoints/*/save',
            ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']
        );
    }
}
