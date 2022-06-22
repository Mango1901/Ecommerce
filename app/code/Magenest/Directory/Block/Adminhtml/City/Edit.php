<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Block\Adminhtml\City;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Magenest\Directory\Block\Adminhtml\City
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
        $this->_controller = 'adminhtml_city';
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
        $city = $this->_coreRegistry->registry('current_city');
        if ($city && $city->getId()) {
            return __("Edit City '%1'", $this->escapeHtml($city->getName()));
        } else {
            return __('New City');
        }
    }
}
