<?php

namespace Magenest\Slider\Block\Adminhtml\Slider\Edit;

use Magenest\Slider\Block\Adminhtml\Template\Slider;
use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
    protected $_options;

    protected $_store;

    protected $_wysiwygConfig;

    protected $_themeLabelFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magenest\Slider\Model\Slider $labelModel,
        \Magento\Store\Model\System\Store $store,
        \Magento\Framework\View\Design\Theme\LabelFactory $themeLabelFactory,
        array $data = []
    ) {
        $this->_options = $labelModel;
        $this->_store = $store;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_themeLabelFactory = $themeLabelFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getModel()
    {
        return $this->_coreRegistry->registry('magenest_slider_slider');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'    => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $fieldset = $form->addFieldset('new_magenest_slider', array());
        $fieldset->addType('slider_main', Slider::class);
        $fieldset->addField(
            'slider_entity',
            'slider_main',
            array(
                'name' => 'slider_entity',
                'class' => 'slider_entity'
            )
        );

        $label = $this->_themeLabelFactory->create();
        $options = $label->getLabelsCollection(__('-- Please Select --'));
        $fieldset_position = $form->addFieldset('add_button_form', ['legend' => __('Slider Position'), 'class' => 'fieldset-wide']);

        $fieldset_position->addField(
            'widget_id',
            'hidden',
            [
                'name' => 'widget_id',
            ]
        );
        $fieldset_position->addField(
            'theme',
            'select',
            [
                'name' => 'theme',
                'label' => __('Design Theme'),
                'title' => __('Design Theme'),
                'class' => 'admin__control-text',
                'required' => true,
                'values' => $options
            ]
        );
        $fieldset_position->addField(
            'store',
            'multiselect',
            [
                'name' => 'store',
                'label' => __('Assign to Store Views'),
                'title' => __('Assign to Store Views'),
                'class' => 'admin__control-text',
                'required' => true,
                'values' => $this->_store->getStoreValuesForForm(false, true)
            ]
        );
        $fieldset_position->addField(
            'sort_order',
            'text',
            [
                'label' => __('Sort Order'),
                'name' => 'sort_order',
                'class' => 'validate-number validate-zero-or-greater integer',
                'required' => true,
                'note' => 'Sort Order of widget instances in the same container'
            ]
        );
        $fieldset_position->addField(
            'pages_display',
            'select',
            [
                'label' => __('Page'),
                'name' => 'pages_display',
                'required' => false,
                'class' => 'admin__control-text',
                'options' => [
                    'default' => 'All Pages',
                    'cms_index_index' => 'Home Page',
                    'catalog_category_view' => 'Catalog Pages',
                    'catalog_product_view' => 'Product Pages',
                    'checkout_onepage_success' => 'One Page Checkout Success',
                ]
            ]
        );
        $fieldset_position->addField(
            'container_display',
            'select',
            [
                'label' => __('Container'),
                'name' => 'container_display',
                'required' => false,
                'class' => 'admin__control-text',
                'options' => [
                    'after.body.start' => 'Page Top',
                    'header.container' => 'Page Header Container',
                    'footer-container' => 'Page Footer Container',
                    'content' => 'Main Content Area',
                    'content.aside' => 'Main Content Aside',
                    'content.bottom' => 'Main Content Bottom',
                    'content.top' => 'Main Content Top',
                    'main' => 'Main Content Container',
                ]
            ]
        );

        if ($model->getId()) {
            $position = json_decode($model->getData('position'), true);
            if ($position) {
                $positionData = [
                    'widget_id' => $position['widget_id'] ?? "",
                    'theme' => $position['theme'],
                    'store' => $position['store'],
                    'sort_order' => $position['sort_order'],
                    'pages_display' => $position['pages_display'],
                    'container_display' => $position['container_display'],
                ];
                $model->setData($positionData);
            }
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
