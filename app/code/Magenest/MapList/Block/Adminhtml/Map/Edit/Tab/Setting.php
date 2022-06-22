<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 16/09/2016
 * Time: 10:14
 */

namespace Magenest\MapList\Block\Adminhtml\Map\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magenest\MapList\Model\Status;

class Setting extends Generic implements TabInterface
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
        $this->setAdvancedLabel("Add Tags");
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('maplist_map_edit');
        $data = $model->getData();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('map_');

        $fieldset = $form->addFieldset(
            'setting_fieldset',
            array(
                'legend' => __('General Settings'),
                'class' => 'fieldset-wide'
            )
        );

        if ($model->getId()) {
            $fieldset->addField(
                'map_id',
                'hidden',
                array('name' => 'map_id')
            );
        }


        $fieldset->addField(
            'title',
            'text',
            array(
                'name' => 'title',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            )
        );

        $fieldset->addField(
            'description',
            'editor',
            array(
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:20em',
                'required' => false,
            )
        );

        $fieldset->addField(
            'config_title',
            'text',
            array(
                'name' => 'config_title',
                'label' => __('Change title "List of Stores"'),
                'title' => __('Change title "List of Stores"'),
                'required' => false
            )
        );

        $fieldset->addField(
            'is_active',
            'select',
            array(
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => array('1' => __('Enable'), '0' => __('Disable'))
            )
        );
        if (!isset($data['is_active'])) {
            $data['is_active'] = 1;
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Map Settings');
    }

    public function getTabTitle()
    {
        return __('Map Settings');
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
