<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MegaMenu\Block\Adminhtml\Label;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Framework\Registry;

/**
 * Class Index
 * @package Magenest\MegaMenu\Block\Adminhtml\Menu
 */
class Edit extends FormContainer
{
    /** @var Registry  */
    protected $_coreRegistry;

    /**
     * Constructor.
     *
     * @param Registry $registry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_objectId = 'label_id';
        $this->_blockGroup = 'Magenest_MegaMenu';
        $this->_controller = 'adminhtml_label';
        $this->_mode       = 'edit';
        parent::_construct();

        if ($this->getRequest()->getParam('id')) {
            // creating duplication url
            $duplicateUrl = $this->getUrl('menu/*/save', ['_current' => true, 'duplicate' => 'edit', 'active_tab' => '']);
            $this->buttonList->add(
                'duplicate',
                [
                    'class' => 'save',
                    'label' => __('Duplicate'),
                    'onclick' => 'setLocation("' . $duplicateUrl . '")'
                ],
                0 // sort order
            );
        }
        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            1
        );
        $this->buttonList->remove('delete');
        $this->buttonList->update('save', 'label', __('Save'));
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('menu/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
