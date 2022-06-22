<?php

namespace Magenest\MapList\Block\Adminhtml\Brand;

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
        $map = $this->_coreRegistry->registry('maplist_brand_edit');
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_MapList';
        $this->_controller = 'adminhtml_brand';
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->buttonList->add(
            'save-and-continue',
            array(
                'label' => __('Save and Continue Edit'),
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

        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        $holiday = $this->coreRegistry->registry('maplist_brand_edit');
        if ($holiday->getId()) {
            return __("Edit Brand", $this->escapeHtml($holiday->getData('name')));
        }

        return __('New Brand');
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