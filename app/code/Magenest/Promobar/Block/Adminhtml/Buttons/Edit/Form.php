<?php

namespace Magenest\Promobar\Block\Adminhtml\Buttons\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('buttons__form');
        $this->setTitle(__('Buttons Information'));
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('promobar_buttons');

        $data = [
            'width' => '100%',
            'height' => '200px',
            'add_variables' => false,
            'add_widgets' => false,
            'add_images' => false
        ];

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
        );

        $fieldset = $form->addFieldset('add_buttons_form', ['legend' => __('Button Information')]);
        $fieldset->addType('customeditbutton', '\Magenest\Promobar\Block\Adminhtml\Template\EditButton');
        $fieldset->addType('customborder', '\Magenest\Promobar\Block\Adminhtml\Template\ChangeBorderWidth');


        if ($model->getId()) {
            $fieldset->addField('button_id', 'hidden', ['name' => 'button_id']);
        }



        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'name' => 'status',
                'required' => false,
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'label' => __('Button Title'),
                'name' => 'title',
                'class' => 'validate-title',
                'required' => true
            ]
        );


        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'style' => 'height:20em',
                'required' => false,
                'wysiwyg' => true,
                'config' => $this->_wysiwygConfig->getConfig($data)
            ]
        );


        $fieldset->addField(
            'size',
            'text',
            [
                'label' => __('Text Size(px)'),
                'name' => 'size',
                'class' => 'validate-size',
                'required' => false
            ]
        );


        $fieldset->addField(
            'text_color',
            'text',
            [
                'label' => __('Color for Text'),
                'name' => 'text_color',
                'required' => false
            ]
        );


        $fieldset->addField(
            'hover_color_text',
            'text',
            [
                'label' => __('Hover Color for Text'),
                'name' => 'hover_color_text',
                'required' => false
            ]
        );

        $borderStyle = new \Magenest\Promobar\Model\Config\Source\BorderStyle;
        $border= $borderStyle->toOptionArray();

        $fieldset->addField(
            'border_style',
            'select',
            [
                'label' => __('Border Style'),
                'name' => 'border_style',
                'required' => false,
                'options' => $border
            ]
        );

        $fieldset->addField(
            'edit_button',
            'customeditbutton',
            [
                'label' => __('Button Setting'),
                'name' => 'edit_button',
                'required' => false
            ]
        );


        $fieldset->addField(
            'background_color',
            'text',
            [
                'label' => __('Background Color for Button'),
                'name' => 'background_color',
                'required' => false
            ]
        );


        $fieldset->addField(
            'hover_color_button',
            'text',
            [
                'label' => __('Hover Color for Button'),
                'name' => 'hover_color_button',
                'required' => false
            ]
        );

        $fieldset->addField(
            'border_width',
            'customborder',
            [
                'label' => __('Border Width'),
                'name' => 'border_width',
                'required' => false
            ]
        );


        $fieldset->addField(
            'background_color_border',
            'text',
            [
                'label' => __('Background Color for Border'),
                'name' => 'background_color_border',
                'required' => false
            ]
        );

        $fieldset->addField(
            'hover_color_border',
            'text',
            [
                'label' => __('Hover Color for Border'),
                'name' => 'hover_color_border',
                'required' => false
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
