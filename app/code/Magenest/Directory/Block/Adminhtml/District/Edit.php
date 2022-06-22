<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Block\Adminhtml\District;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Magenest\Directory\Block\Adminhtml\District
 */
class Edit extends Container
{
    /**
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    )
    {

        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_Directory';
        $this->_controller = 'adminhtml_district';
        parent::_construct();
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderText()
    {
        $district = $this->_coreRegistry->registry('current_district');
        if ($district && $district->getId()) {
            return __("Edit District '%1'", $this->escapeHtml($district->getName()));
        } else {
            return __('New District');
        }
    }
}
