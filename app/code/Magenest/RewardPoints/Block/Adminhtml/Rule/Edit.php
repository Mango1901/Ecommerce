<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Rule;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Magenest\RewardPoints\Block\Adminhtml\Rule
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
        $templates = $this->_coreRegistry->registry('rewardpoints_rule');

        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_RewardPoints';
        $this->_controller = 'adminhtml_rule';
        parent::_construct();
        if (!$templates->getId()) {
            $this->buttonList->remove('save');
        } else {
            $this->buttonList->update('save', 'label', __('Save Rule'));
            $this->buttonList->update('save', 'onclick', 'jQuery("body").loader("show");');
        }
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );

        $this->buttonList->update('delete', 'label', __('Delete Rule'));
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeaderText()
    {
        $templates = $this->_coreRegistry->registry('rewardpoints_rule');
        if ($templates->getId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($templates->getTitle()));
        }
        return __('New Rule');
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
