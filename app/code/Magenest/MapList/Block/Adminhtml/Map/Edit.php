<?php

namespace Magenest\MapList\Block\Adminhtml\Map;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Framework\Registry;

class Edit extends FormContainer
{
    protected $_coreRegistry;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $map = $this->_coreRegistry->registry('maplist_map_edit');
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_MapList';
        $this->_controller = 'adminhtml_map';
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Import'));
        $this->buttonList->add(
            'save-and-continue',
            array(
                'label' => __('Import and Continue Edit'),
                'class' => 'save',
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        )
                    )
                )
            ),
            -100
        );
    }

    public function getHeaderText()
    {
        $map = $this->_coreRegistry->registry('maplist_map_edit');
        if ($map->getId()) {
            return __("Edit Map", $this->escapeHtml($map->getData('name')));
        }

        return __('New Map');
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'maplist/*/save',
            array(
                '_current' => true,
                'back' => 'edit',
                'active_tab' => '{{tab_id}}'
            )
        );
    }
}
