<?php

namespace Magenest\Promobar\Block\Adminhtml\Promobars\Edit;


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

    protected $_themeLabelFactory;

    protected $_storeManager;

    protected $_store;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Store\Model\System\Store
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\System\Store $store,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\View\Design\Theme\LabelFactory $themeLabelFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_store = $store;
        $this->_themeLabelFactory = $themeLabelFactory;
        $this->_systemStore = $systemStore;
        $this->_storeManager = $context->getStoreManager();
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
        $this->setId('promobars__form');
        $this->setTitle(__('Promobars Information'));
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('promobar_promobars');
        $mobileModel = $this->_coreRegistry->registry('promobar_mobile_promobars');

        $data = [
            'width' => '100%',
            'height' => '200px',
            'add_variables' => false,
            'add_widgets' => true,
            'add_images' => true
        ];

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
        );
        $fieldset = $form->addFieldset('add_promobars_form', ['legend' => __('Desktop   Bar Setting')]);
        $fieldsetMobile = $form->addFieldset('add_mobile_promobars_form', ['legend' => __('Mobile Bar Setting')]);
        $fieldset_position = $form->addFieldset('add_button_form', ['legend' => __('Generate Widget')]);

        //template for bar
        $fieldset->addType('customimage', '\Magenest\Promobar\Block\Adminhtml\Template\BackgroundImage');
        $fieldset->addType('customedit', '\Magenest\Promobar\Block\Adminhtml\Template\EditImage');
        $fieldset->addType('customposition', '\Magenest\Promobar\Block\Adminhtml\Template\PositionButton');
        $fieldset->addType('customheight', '\Magenest\Promobar\Block\Adminhtml\Template\ChangeHeightBar');
        $fieldset->addType('custompositiontext', '\Magenest\Promobar\Block\Adminhtml\Template\PositionText');
        $fieldset->addType('custommultiplecontent', '\Magenest\Promobar\Block\Adminhtml\Template\MultipleContent');


        //template for mobile bar
        $fieldsetMobile->addType('mobilecustomedit', '\Magenest\Promobar\Block\Adminhtml\Template\Mobile\MobileEditImage');
        $fieldsetMobile->addType('mobilecustomposition', '\Magenest\Promobar\Block\Adminhtml\Template\Mobile\MobilePositionButton');
        $fieldsetMobile->addType('mobilecustomheight', '\Magenest\Promobar\Block\Adminhtml\Template\Mobile\MobileChangeHeightBar');
        $fieldsetMobile->addType('mobilecustompositiontext', '\Magenest\Promobar\Block\Adminhtml\Template\Mobile\MobilePositionText');

        if ($mobileModel->getId()) {
            $fieldsetMobile->addField('mobile_promobar_id', 'hidden', ['name' => 'mobile_promobar_id']);
        }

        $fieldset_position->addType('createwidget', '\Magenest\Promobar\Block\Adminhtml\Template\CreateWidget');

        $useSameConfigAsDesktop = $fieldsetMobile->addField(
            'use_same_config',
            'select',
            [
                'label' => __('Use same configuration as desktop'),
                'name' => 'use_same_config',
                'required' => false,
                'options' => [
                    '0' => 'No',
                    '1' => 'Yes'

                ]
            ]
        );

        $fieldsetMobile->addField(
            'breakpoint',
            'text',
            [
                'label' => __('Breakpoint'),
                'name' => 'breakpoint',
                'class' => 'validate-number validate-zero-or-greater',
                'required' => false,
                'note' => 'Mobile promo bar style will be applied if screen width is less than breakpoint value'
            ]
        );


        $mobileBarHeight = $fieldsetMobile->addField(
            'mobile_height_pro_bar',
            'mobilecustomheight',
            [
                'label' => __('Mobile Bar Height'),
                'name' => 'mobile_height_pro_bar',
                'required' => false
            ]
        );

        $mobileBarEditImage = $fieldsetMobile->addField(
            'mobile_edit_image',
            'mobilecustomedit',
            [
                'label' => __('Mobile Background Image Setting'),
                'name' => 'mobile_edit_background',
                'required' => false
            ]
        );

        $mobileBarTextSize = $fieldsetMobile->addField(
            'mobile_bar_text_size',
            'text',
            [
                'label' => __('Mobile Text Size(px)'),
                'name' => 'mobile_bar_text_size',
                'required' => false,
                'class' => 'validate-size',
            ]
        );

        $mobileBarPositionText = $fieldsetMobile->addField(
            'mobile_bar_position_text',
            'mobilecustompositiontext',
            [
                'label' => __('Mobile Text vertical alignment'),
                'name' => 'mobile_bar_position_text',
                'required' => false
            ]
        );

        $mobileBarPositionButton = $fieldsetMobile->addField(
            'mobile_bar_position_button',
            'mobilecustomposition',
            [
                'name' => 'mobile_bar_position_button',
                'label' => __('Mobile Position Button'),
                'title' => __('Mobile Position Button'),
                'required' => false,
            ]
        );

        if ($model->getId()) {
            $fieldset->addField('promobar_id', 'hidden', ['name' => 'promobar_id']);
            $fieldset->addField('instance_id_widget', 'hidden', ['name' => 'instance_id_widget']);
        }

        $fieldset->addField('status_image', 'hidden', ['name' => 'status_image']);


        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'name' => 'status',
                'required' => false,
                'options' => ['0' => __('Enabled'), '1' => __('Disabled')]
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'label' => __('Bar Title'),
                'name' => 'title',
                'required' => true
            ]
        );

        $fieldset->addField(
            'change_height',
            'customheight',
            [
                'label' => __('Bar Height'),
                'name' => 'change_height',
                'required' => false
            ]
        );

        $fieldset->addField(
            'background_image',
            'customimage',
            [
                'name' => 'background_image',
                'label' => __('Background Image'),
                'title' => __('Background Image'),
                'required' => false,
                'note' => 'Allow image type: jpg, gif, jpeg, png',
            ]
        );
        $fieldset->addField(
            'edit_image',
            'customedit',
            [
                'name' => 'edit_image',
                'label' => __('Background Image Setting'),
                'title' => __('Background Image Setting'),
                'required' => false,
            ]
        );


        $fieldset->addField(
            'multiple_content',
            'custommultiplecontent',
            [
                'label' => __('Sliders'),
                'name' => 'multiple_content',
                'required' => false
            ]
        );

        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'style' => 'height:20em',
                'required' => true,
                'wysiwyg' => true,
                'config' => $this->_wysiwygConfig->getConfig($data),
            ]
        );

        $fieldset->addField(
            'size',
            'text',
            [
                'label' => __('Text Size(px)'),
                'name' => 'size',
                'required' => false,
                'class' => 'validate-size'
            ]
        );

        $fieldset->addField(
            'delay_content',
            'text',
            [
                'label' => __('Time life for each content (seconds)'),
                'name' => 'delay_content',
                'class' => 'validate-time-number',
                'required' => false
            ]
        );

        $fieldset->addField(
            'background_text',
            'text',
            [
                'label' => __('Text Color'),
                'name' => 'background_text',
                'required' => false
            ]
        );


        $fieldset->addField(
            'background_color',
            'text',
            [
                'label' => __('Background Color'),
                'name' => 'background_color',
                'required' => false
            ]
        );



        $fieldset->addField(
            'position_text',
            'custompositiontext',
            [
                'label' => __('Text vertical alignment'),
                'name' => 'position_text',
                'required' => false
            ]
        );


        $optionButton = new \Magenest\Promobar\Model\Config\Source\TypeButton;
        $typeButton = $optionButton->toOptionArray();

        $fieldset->addField(
            'button_id',
            'select',
            [
                'label' => __('Type Button'),
                'name' => 'button_id',
                'required' => false,
                'options' => $typeButton
            ]
        );

        $fieldset->addField(
            'position_button',
            'customposition',
            [
                'name' => 'position_button',
                'label' => __('Position Button'),
                'title' => __('Position Button'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'url',
            'text',
            [
                'label' => __('Button URL'),
                'name' => 'url',
                'required' => false
            ]
        );


        $fieldset->addField(
            'allow_closed',
            'select',
            [
                'label' => __('Show button close(x)'),
                'name' => 'allow_closed',
                'required' => false,
                'options' => [
                    '0' => 'No',
                    '1' => 'Yes'
                ]
            ]
        );

        $fieldset->addField(
            'delay_time',
            'text',
            [
                'label' => __('Display with delay after page load (seconds)'),
                'name' => 'delay_time',
                'class' => 'validate-time-number',
                'required' => false
            ]
        );

        $fieldset->addField(
            'time_life',
            'text',
            [
                'label' => __('Auto-hide after (seconds)'),
                'name' => 'time_life',
                'class' => 'validate-time-number',
                'required' => false
            ]
        );
        $fieldset->addField(
            'sticky',
            'select',
            [
                'label' => __('Sticky'),
                'name' => 'sticky',
                'required' => false,
                'options' => [
                    '0' => 'Yes',
                    '1' => 'No',
                ]
            ]
        );

        //row setting position bar
        $label = $this->_themeLabelFactory->create();
        $options = $label->getLabelsCollection(__('-- Please Select --'));

        $fieldset_position->addField(
            'theme',
            'select',
            [
                'name' => 'theme',
                'label' => __('Design Theme'),
                'title' => __('Design Theme'),
                'required' => false,
                'values' => $options,
            ]
        );

        $fieldset_position->addField(
            'store',
            'multiselect',
            [
                'name' => 'store',
                'label' => __('Assign to Store Views'),
                'title' => __('Assign to Store Views'),
                'required' => false,
                'values' => $this->_store->getStoreValuesForForm(false, true)
            ]
        );

        $fieldset_position->addField(
            'sort_order',
            'text',
            [
                'label' => __('Sort Order'),
                'name' => 'sort_order',
                'class' => 'validate-time-number',
                'required' => false,
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
                'options' => [
                    'default' => 'All Pages',
                    'cms_index_index' => 'Home Page',
                    'catalog_category_view' => 'Catalog Pages',
                    'catalog_product_view' => 'Product Pages',
                    'checkout_index_index' => 'Checkout Pages',
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
                'options' => [
                    'after.body.start' => 'Page Top',
                    'header.container' => 'Page Header Container',
                    'footer-container' => 'Page Footer Container'
                ]
            ]
        );

        $fieldset_position->addField(
            'createwidget',
            'createwidget',
            [
                'label' => __('New Widget'),
                'name' => 'createwidget',
                'note' => 'Create widget and save pomobar after click button'
            ]
        );


        $mobileMultipleContent = json_decode($mobileModel->getData('mobile_multiple_content'),true);
        $content = json_decode($mobileMultipleContent[0]['content'],true);
        $mobileTextSize = $content['size'];
        $model->setData('mobile_bar_text_size', $mobileTextSize);
        $model->setData('use_same_config', $mobileModel->getData('use_same_config'));
        $model->setData('breakpoint', $mobileModel->getData('breakpoint'));
//        $values = array_merge($mobileModel->getData(), $model->getData());
        $form->setValues($model->getData());
//        $form->setValues($values);
//        $form->setValues($mobileModel->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
