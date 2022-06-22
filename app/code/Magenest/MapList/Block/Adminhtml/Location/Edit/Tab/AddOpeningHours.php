<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MapList\Block\Adminhtml\Location\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magenest\MapList\Model\Status;

class AddOpeningHours extends Generic implements TabInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $status,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data
    ) {
        $this->_status = $status;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('maplist_location_edit');
        $dataModels = $model->getData();
        $data = array();
        foreach ($dataModels as $dataModel => $value) {
            if ($dataModel == 'opening_hours') {
                if ($value) {
                    $data = array_merge($data, json_decode($value, true));
                }
            } else {
                $data[$dataModel] = $value;
            }
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('location_');

        $fieldset = $form->addFieldset(
            'opening_hours_fieldset',
            array(
                'legend' => __('Opening Hours'),
            )
        );
        if ($model->getId()) {
            $fieldset->addField(
                'location_id',
                'hidden',
                array('name' => 'location_id')
            );
        }

        $fieldset->addField(
            'is_use_default_opening_hours',
            'checkbox',
            array(
                'name' => 'is_use_default_opening_hours',
                'label' => __('Use Default Hours'),
                'title' => __('Use Default Hours'),
                'required' => false,
                'onclick' => 'this.value = this.checked ? 1 : 0;',
                'checked' => isset($data['is_use_default_opening_hours']) ? $data['is_use_default_opening_hours'] : 1,
                'note' => '</br><a style="font-size: 15px;color:black;text-decoration: none;">2Format Opening Hours: (open Hours)HH:mm - (close Hours)HH:mm (24 Hours format)
                        <br>Leave field empty for day off</a>'
            )
        );
        $fieldset->addField(
            'opening_hours_monday',
            'text',
            array(
                'name' => 'opening_hours[opening_hours_monday]',
                'label' => __('Monday'),
                'title' => __('Monday'),
                'class' => 'validate-opening-hours',
                'required' => false
            )
        );
        $fieldset->addField(
            'opening_hours_tuesday',
            'text',
            array(
                'name' => 'opening_hours[opening_hours_tuesday]',
                'label' => __('Tuesday'),
                'title' => __('Tuesday'),
                'class' => 'validate-opening-hours',
                'required' => false
            )
        );
        $fieldset->addField(
            'opening_hours_wednesday',
            'text',
            array(
                'name' => 'opening_hours[opening_hours_wednesday]',
                'label' => __('Wednesday'),
                'title' => __('Wednesday'),
                'class' => 'validate-opening-hours',
                'required' => false
            )
        );
        $fieldset->addField(
            'opening_hours_thursday',
            'text',
            array(
                'name' => 'opening_hours[opening_hours_thursday]',
                'label' => __('Thursday'),
                'title' => __('Thursday'),
                'class' => 'validate-opening-hours',
                'required' => false
            )
        );
        $fieldset->addField(
            'opening_hours_friday',
            'text',
            array(
                'name' => 'opening_hours[opening_hours_friday]',
                'label' => __('Friday'),
                'title' => __('Friday'),
                'class' => 'validate-opening-hours',
                'required' => false
            )
        );
        $fieldset->addField(
            'opening_hours_saturday',
            'text',
            array(
                'name' => 'opening_hours[opening_hours_saturday]',
                'label' => __('Saturday'),
                'title' => __('Saturday'),
                'class' => 'validate-opening-hours',
                'required' => false
            )
        );
        $fieldset->addField(
            'opening_hours_sunday',
            'text',
            array(
                'name' => 'opening_hours[opening_hours_sunday]',
                'label' => __('Sunday'),
                'title' => __('Sunday'),
                'class' => 'validate-opening-hours',
                'required' => false
            )
        );
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm(); // TODO: Change the autogenerated stub
    }

    public function getTabLabel()
    {
        return __('Opening Hours');
    }

    public function getTabTitle()
    {
        return __('Opening Hours');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
